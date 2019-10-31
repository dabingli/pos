<?php
namespace app\modules\v1\actions\cashier;

use yii;
use yii\Base\Action;
use common\models\entities\User;
use common\models\entities\UserRankCashierAgent;

class SeeAction extends Action
{

    public $amount;

    public $cashierAgentId;

    public function run()
    {
        $model = UserRankCashierAgent::findOne([
            'user_rank_id' => Yii::$app->user->identity->userRank->id,
            'cashier_agent_id' => $this->cashierAgentId
        ]);
        $arriveMoney = sprintf("%.2f", $this->amount - $model->getFee($this->amount) - Yii::$app->user->identity->userRank->cash_fee);
        
        $data['arriveMoney'] = $arriveMoney;
        $data['fee_money'] = sprintf("%.2f", $model->getFee($this->amount) + Yii::$app->user->identity->userRank->cash_fee);
        $data['money'] = sprintf("%.2f", $this->amount);
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }
}