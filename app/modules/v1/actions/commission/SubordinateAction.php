<?php
namespace app\modules\v1\actions\commission;

use yii;
use yii\base\Action;
use common\models\entities\nestedSets\UserLink;
use common\models\entities\User;

/**
 * 下级
 *
 * @author Administrator
 *        
 */
class SubordinateAction extends Action
{

    public $page = 1;

    public $limit;

    /**
     * 1实名验证
     * 2未实名验证
     *
     * @var unknown
     */
    public $real;

    public $userId;

    public function run()
    {
        $model = UserLink::findOne(Yii::$app->user->id);
        $childrenModel = $model->children();
        $childrenModel->andWhere([
            'a.agent_id' => Yii::$app->params['agentModel']->id
        ]);
        $childrenModel->alias('a');
        $childrenModel->select([
            'user_id'
        ]);
        $sql = $childrenModel->createCommand()->getRawSql();
        // userId必须是属于该用户的子子孙孙
        $model = User::find();
        $model->andWhere("id IN ($sql)");
        $model->andWhere([
            'parent_id' => $this->userId
        ]);
        $model->andWhere([
            'agent_id' => Yii::$app->params['agentModel']->id
        ]);
        if ($this->real == 1) {
            $model->andWhere([
                'is_authentication' => User::AUTH_YES
            ]);
        } elseif ($this->real == 2) {
            $model->andWhere([
                '!=',
                'is_authentication',
                User::AUTH_YES
            ]);
        }
        $model->with([
            'appUser' => function ($q) {
                $q->select([
                    'id',
                    'app_sensitive',
                    'agent_id'
                ]);
            }
        ]);
        
        $model->select([
            'id',
            'user_code',
            'mobile',
            'user_name',
            'wallet_money',
            'frozen_money',
            'repayment_money',
            'parent_id',
            'email',
            'user_rank_id',
            'status',
            'sex',
            'login_time',
            'is_authentication',
            'authentication_time',
            'app_id',
            'agent_id'
        ]);
        $data = $model->all();
        foreach ($data as &$m) {
            $m->mobile = $m->getMobileSensitive();
            $m->user_name = $m->getUserNameSensitive();
        }
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }
}