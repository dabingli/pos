<?php
namespace app\modules\v1\actions\order;

use yii;
use yii\base\Action;
use common\library\automatic\perfect\RepaymentRobot;
use common\models\entities\UserBankCard;
use common\models\services\app\ListOrderForm;

class ListOrder extends Action
{

    public $status;

    public $page = 1;

    public $limit;

    public function run()
    {
        $model = ListOrderForm::find();
        $model->andWhere([
            'user_id' => Yii::$app->user->id,
            'agent_id' => Yii::$app->params['agentModel']->id
        ]);
        $model->andFilterWhere([
            'status' => $this->status
        ]);
        $offset = ($this->page - 1) * $this->limit;
        $model->offset($offset)->limit($this->limit);
        $model->with('billsUserPlanOrderConsume');
        $model->with('billsUserPlanOrderEpayment');
        $data = $model->asArray()->all();
        foreach ($data as $k => $m) {
            $data[$k]['orderSn'] = 'T' . sprintf("%09d", $m['id']);
        }
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }
}