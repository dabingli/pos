<?php
namespace app\modules\v1\actions\order;

use yii;
use yii\base\Action;
use common\models\entities\BillsUserPlanOrder;

class View extends Action
{

    public $id;

    public function run()
    {
        $model = BillsUserPlanOrder::find();
        $model->andWhere([
            'bills_user_plan_id' => $this->id,
            'agent_id' => Yii::$app->params['agentModel']->id,
            'user_id' => Yii::$app->user->id
        ]);
        $model->asArray();
        $data = $model->all();
        $datas = [];
        $datas['repayment'] = 0;
        $datas['consumption'] = 0;
        foreach ($data as $val) {
            if (BillsUserPlanOrder::SUCCESS == $val['status']) {
                if ($val['type'] == BillsUserPlanOrder::CONSUME) {
                    // 消费
                    $datas['consumption'] += $val['amount'];
                } elseif ($val['type'] == BillsUserPlanOrder::EPAYMENT) {
                    // 还款
                    $datas['repayment'] += $val['amount'];
                }
            }
        }
        $datas['list'] = $data;
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $datas
        ];
    }
}