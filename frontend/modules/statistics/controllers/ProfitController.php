<?php
namespace frontend\modules\statistics\controllers;

use common\models\CashOrder;
use yii;
use common\helpers\FormHelper;
use common\models\user\User;
use common\models\statistics\User as StatisticsUser;
use common\models\Profit;
use common\models\MerchantUser;

class ProfitController extends \frontend\controllers\MController
{

    public function actionIndex()
    {

        $data = [
            'platformReturnProfit' => $this->getPlatformReturnProfit(),
            'platformProfit' => $this->getPlatformProfit(),
            'returnProfit' => $this->getReturnProfit(),
            'profit' => $this->getProfit(),
            'cashReturnProfit' => $this->getCashReturnProfit(CashOrder::RETURN_CASH),
            'cashProfit' => $this->getCashReturnProfit(CashOrder::PROFIT),
        ];

        return $this->render('index', $data);
    }

    /**
     * @ 获取平台返现
     * @return mixed
     */
    public function getPlatformReturnProfit()
    {
        $model = Profit::find();
        $model->andWhere([
            'in',
            'user_id',
            $this->agentAppUser->id
        ]);

        $model->andWhere([
            'in',
            'type',
            [Profit::ACTIVATION_RETURN, Profit::FROZEN_RETURN, Profit::RETURN_REWARDS]
        ]);

        $model->andWhere([
            'agent_id' => $this->agentId
        ]);

        return $model->sum('amount_profit');
    }

    /**
     * @ 获取平台分润
     * @return mixed
     */
    public function getPlatformProfit()
    {
        $model = Profit::find();
        $model->andWhere([
            'in',
            'user_id',
            $this->agentAppUser->id
        ]);

        $model->andWhere([
            'in',
            'type',
            [Profit::TRANSACTION_DISTRIBUTION, Profit::FROZEN_DISTRIBUTION]
        ]);

        $model->andWhere([
            'agent_id' => $this->agentId
        ]);

        return $model->sum('amount_profit');
    }

    /**
     * @ 获取代理商返现
     * @return mixed
     */
    public function getReturnProfit()
    {
        $model = Profit::find();
        $model->andWhere([
            'not in',
            'user_id',
            $this->agentAppUser->id
        ]);

        $model->andWhere([
            'in',
            'type',
            [Profit::ACTIVATION_RETURN, Profit::FROZEN_RETURN, Profit::RETURN_REWARDS]
        ]);

        $model->andWhere([
            'agent_id' => $this->agentId
        ]);

        return $model->sum('amount_profit');
    }

    /**
     * @ 获取代理商分润
     * @return mixed
     */
    public function getProfit()
    {
        $model = Profit::find();
        $model->andWhere([
            'not in',
            'user_id',
            $this->agentAppUser->id
        ]);

        $model->andWhere([
            'in',
            'type',
            [Profit::TRANSACTION_DISTRIBUTION, Profit::FROZEN_DISTRIBUTION]
        ]);

        $model->andWhere([
            'agent_id' => $this->agentId
        ]);

        return $model->sum('amount_profit');
    }

    /**
     * @ 获取代理商提现返现
     * @return mixed
     */
    public function getCashReturnProfit($type=1)
    {
        $model = CashOrder::find();
        $model->andWhere([
            'not in',
            'user_id',
            $this->agentAppUser->id
        ]);

        $model->andWhere([
            'type' => $type
        ]);

        $model->andWhere([
            'agent_id' => $this->agentId
        ]);

        return $model->sum('cash_amount');
    }
}