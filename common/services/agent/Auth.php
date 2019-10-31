<?php
namespace common\services\agent;

use Yii;
use common\models\agent\AuthItem;
use common\services\Service;
use common\models\agent\AuthItemChild;
use common\helpers\ArrayHelper;

class Auth extends Service
{

    /**
     * 当前用户权限
     *
     * @var \common\models\sys\AuthItem
     */
    protected $role;

    /**
     * 当前系统权限
     *
     * @var array
     */
    protected $sysAuth = [];

    /**
     * 获取当前的角色
     *
     * @return AuthItem|null
     */
    public function getRole()
    {
        if (! $this->role) {
            if ($assignment = Yii::$app->user->identity->assignment) {
                $this->role = AuthItem::find()->where([
                    'id' => $assignment->item_id,
                    'type' => AuthItem::ROLE
                ])
                    ->limit(1)
                    ->one();
            }
        }
        
        return $this->role;
    }

    /**
     * 获取当前用户下能管辖的所有角色
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChildRoles()
    {
        // 样式渲染辅助
        $treeStat = 1;
        // 如果不是总管理，只显示自己能管辖的角色
        if (! Yii::$app->services->agent->isAuperAdmin()) {
            $role = $this->getRole();
            $parent_key = $role->key;
            $treeStat += $role->level;
            $models = AuthItem::getChilds($role);
        } else {
            $models = AuthItem::find()->where([
                'type' => AuthItem::ROLE,
                'agent_id' => Yii::$app->params['agentModel']->id
            ])
                ->orderBy('sort asc')
                ->asArray()
                ->all();
            
            $parent_key = 0;
        }
        
        return [
            $models,
            $parent_key,
            $treeStat
        ];
    }

    /**
     * 获取当前用户权限的下面的所有用户id
     *
     * @return array
     */
    public function getChildRoleIds()
    {
        if (Yii::$app->services->agent->isAuperAdmin()) {
            return [];
        }
        
        $role = $this->getRole();
        $position = $role->position . ' ' . AuthItem::POSITION_PREFIX . $role->key;
        $models = AuthItem::getMultiDate([
            'like',
            'position',
            $position . '%',
            false
        ], [
            '*'
        ], 'level asc', [
            'authAssignments'
        ]);
        
        $ids = [];
        foreach ($models as $model) {
            foreach ($model['authAssignments'] as $authAssignments) {
                $ids[] = $authAssignments['user_id'];
            }
        }
        
        return ! empty($ids) ? $ids : [
            - 1
        ];
    }

    /**
     * 获取用户权限jsTree数据
     *
     * @param string $name
     *            角色名称
     * @return array
     */
    public function getAuthJsTreeData($name)
    {
        // 获取当前登录的所有权限
        $auths = $this->getUserAuth();
        // 获取当前角色权限
        $authItemChild = AuthItemChild::find()->where([
            'parent' => $name
        ])
            ->with([
            'child0'
        ])
            ->asArray()
            ->all();
        
        $checkIds = [];
        $tmpChildCount = [];
        
        foreach ($authItemChild as $value) {
            $checkIds[] = $value['child0']['key'];
            
            // 统计他自己出现次数
            if ($value['child0']['parent_key'] > 0) {
                ! isset($tmpChildCount[$value['child0']['parent_key']]) ? $tmpChildCount[$value['child0']['parent_key']] = 1 : $tmpChildCount[$value['child0']['parent_key']] += 1;
            }
        }
        $formAuth = []; // 全部权限
        $tmpAuthCount = [];
        foreach ($auths as $auth) {
            $formAuth[] = [
                'id' => $auth['key'],
                'parent' => ! empty($auth['parent_key']) ? $auth['parent_key'] : '#',
                'text' => $auth['description']
                // 'icon' => 'none'
            ];
            
            $count = count(ArrayHelper::getChildIds($auths, $auth['key'], 'key', 'parent_key'));
            $tmpAuthCount[$auth['key']] = $count == 0 ? 1 : $count;
        }
        
        // 做一次筛选，不然jstree会把顶级分类下所有的子分类都选择
        foreach ($tmpChildCount as $key => $item) {
            if (isset($tmpAuthCount[$key]) && $item != $tmpAuthCount[$key]) {
                $checkIds = array_merge(array_diff($checkIds, [
                    $key
                ]));
            }
        }
        
        unset($tmpChildCount, $tmpChildCount, $auths);
        $checkIds = ! empty($checkIds) ? array_filter($checkIds) : [];
        return [
            $formAuth,
            $checkIds
        ];
    }

    /**
     * 获取当前自己拥有的所有权限
     *
     * @param
     *            $name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserAuth()
    {
        if (! Yii::$app->services->agent->isAuperAdmin()) {
            if (! ($role = $this->getRole())) {
                return [];
            }
            
            $models = AuthItemChild::find()->where([
                'parent' => $role->id
            ])
                ->asArray()
                ->all();
            
            $childs = array_column($models, 'child');
            
            return AuthItem::find()->where([
                'type' => AuthItem::AUTH
            ])
                ->andWhere([
                'in',
                'name',
                $childs
            ])
                ->orderBy('sort asc')
                ->asArray()
                ->all();
        }
        
        return AuthItem::find()->where([
            'type' => AuthItem::AUTH
        ])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
    }
}