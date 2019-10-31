<?php
namespace app\modules\v1\actions\commission;

use yii;
use yii\base\Action;
use common\models\entities\nestedSets\UserLink;
use common\models\entities\User;

/**
 * 子用户，第一级
 *
 * @author Administrator
 *        
 */
class SonAction extends Action
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

    public function run()
    {
        $model = User::find();
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
        $model->andWhere([
            'parent_id' => Yii::$app->user->id
        ]);
        $offset = ($this->page - 1) * $this->limit;
        $model->limit($this->limit)->offset($offset);
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
        $model->with([
            'appUser' => function ($q) {
                $q->select([
                    'id',
                    'app_sensitive',
                    'agent_id'
                ]);
            }
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