<?php
namespace frontend\modules\sys\controllers;

use yii;
use common\helpers\ArrayHelper;
use common\helpers\ResultDataHelper;
use common\models\agent\AuthItem;
use common\models\agent\AuthItemChild;

class AuthRoleController extends \frontend\controllers\MController
{

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        list ($models, $parent_key, $treeStat) = Yii::$app->services->agent->auth->getChildRoles();
        
        return $this->render('index', [
            'models' => ArrayHelper::itemsMerge($models, $parent_key, 'key', 'parent_key'),
            'treeStat' => $treeStat
        ]);
    }

    /**
     * 角色授权
     *
     * @return array|string
     * @throws yii\base\InvalidConfigException
     * @throws yii\db\Exception
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel([
            'id' => $id,
            'agent_id' => $this->agentId,
            'type' => AuthItem::ROLE
        ]);
        if ($request->isAjax) {
            $model->attributes = $request->post();
            
            //$model->description = Yii::$app->user->identity->account . '添加了角色';
            if (! $model->save()) {
                return ResultDataHelper::json(422, $this->analyErr($model->getFirstErrors()));
            }
            
            $userTreeIds = $request->post('userTreeIds', []);
            $plugTreeIds = $request->post('plugTreeIds', []);
            
            // 增加的用户权限
            $addAuths = AuthItem::find()->where([
                'type' => AuthItem::AUTH
            ])
                ->andWhere([
                'in',
                'key',
                $userTreeIds
            ])
                ->select('name')
                ->asArray()
                ->all();
            
            // 校验是否在自己的权限下
            $useAuth = Yii::$app->services->agent->auth->getUserAuth();
            $allAuth = array_merge(array_intersect(array_column($useAuth, 'name'), array_column($addAuths, 'name')));
            
            if (! (AuthItemChild::accredit($model->id, $allAuth))) {
                return $this->message("权限提交失败", $this->redirect([
                    'index'
                ]), 'error');
            }
            /**
             * 记录行为日志
             *
             * 由于数据与预期的不符手动写入Post数据
             */
            Yii::$app->request->setBodyParams(ArrayHelper::merge($request->post(), [
                'userTrees' => $allAuth
            ]));
            Yii::$app->services->agent->log('authEdit', '创建/编辑角色 or 权限');

            return $this->message("提交成功", $this->redirect([
                'index'
            ]));
        }
        
        $sysAuth = Yii::$app->services->agent->auth;
        // 当前用户权限
        $name = $model->name;
        list ($userTreeData, $userTreeCheckIds) = $sysAuth->getAuthJsTreeData($model->id);
        // jq冲突禁用
        $this->forbiddenJq();
        
        return $this->render('edit', [
            'model' => $model,
            'userTreeData' => $userTreeData,
            'userTreeCheckIds' => $userTreeCheckIds,
            'plugTreeData' => [],
            'plugTreeCheckIds' => [],
            'name' => $name,
            'parentTitle' => $request->get('parent_title', '无'),
            'parentKey' => $request->get('parent_key', 0)
        ]);
    }

    /**
     * 删除
     *
     * @param
     *            $name
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        // 记录行为日志
        Yii::$app->services->agent->log('authDel', '删除角色');
        $model = $this->findModel([
            'id' => $id,
            'agent_id' => $this->agentId,
            'type' => AuthItem::ROLE
        ]);
        if ($this->findModel([
            'id' => $id,
            'agent_id' => $this->agentId,
            'type' => AuthItem::ROLE
        ])->delete()) {
            return $this->message("删除成功", $this->redirect([
                'index'
            ]));
        }
        
        return $this->message("删除失败", $this->redirect([
            'index'
        ]), 'error');
    }

    /**
     * ajax更新排序/状态
     *
     * @param
     *            $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (! ($model = AuthItem::findOne([
            'key' => $id
        ]))) {
            return $this->message("找不到数据", $this->redirect([
                'index'
            ]), 'error');
        }
        
        $data = ArrayHelper::filter(Yii::$app->request->get(), [
            'sort',
            'status'
        ]);
        $model->attributes = $data;
        if (! $model->save()) {
            return $this->message("修改失败", $this->redirect([
                'index'
            ]), 'error');
        }

        return $this->message("修改成功", $this->redirect([
            'index'
        ]));    }

    /**
     * 由于jstree会和系统的js引入冲突，先设置禁用掉
     *
     * @throws yii\base\InvalidConfigException
     */
    private function forbiddenJq()
    {
        Yii::$app->set('assetManager', [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => []
                ]
            ]
        ]);
    }

    /**
     * 返回模型
     *
     * @param
     *            $id
     * @return mixed
     */
    protected function findModel($id)
    {
        if (empty($id) || empty(($model = AuthItem::findOne($id)))) {
            $model = new AuthItem();
            $model = $model->loadDefaultValues();
            $model->type = AuthItem::ROLE;
            $model->agent_id = $this->agentId;
            return $model;
        }
        
        return $model;
    }
}