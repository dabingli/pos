<?php
namespace common\services;

use common\models\agent\Agent;
use yii;
use yii\base\BaseObject;
use common\models\CashOrder;
use common\models\Order;
use common\models\user\User;

/**
 * 提现
 *
 * @author Administrator
 *
 */
class Withdraw extends BaseObject
{

    public $outerTradeNo;

    protected $cashOrder;

    protected $order;

    protected $agent;

    protected $user;

    public $receiveOrder;

    public function handleOrder()
    {
        $this->cashOrder = CashOrder::findOne([
            'order' => $this->outerTradeNo
        ]);

        if (CashOrder::HANDLE != $this->cashOrder->status) {
            return false;
        }
        $this->order = Order::findOne([
            'order' => $this->outerTradeNo
        ]);
        $this->agent = Agent::findOne([
            'id' => $this->cashOrder->agent_id
        ]);
        $this->user = User::findOne([
            'id' => $this->cashOrder->user_id
        ]);
        if (null === $this->receiveOrder) {

            return false;
        }
        if (empty($this->receiveOrder)) {
            $this->fail();
            return true;
        } else {
            $this->success();
            return true;
        }
    }

    public function success()
    {
        $this->cashOrder->status = CashOrder::SUCCESS;
        $this->cashOrder->save();
        $this->order->status = Order::SUCCESS;
        $this->order->save();
        return true;
    }

    public function fail()
    {
        $this->cashOrder->status = CashOrder::FAIL;
        $this->cashOrder->save();
        $this->order->status = Order::FAIL;
        $this->order->save();

        if (CashOrder::RETURN_CASH == $this->cashOrder['type']) {
            $total_money = $this->cashOrder['cash_amount'];
            User::updateAllCounters([
                'activate_money' => $total_money
            ], [
                'id' => $this->user['id']
            ]);
        } else {
            $total_money = $this->cashOrder['cash_amount'];
            User::updateAllCounters([
                'profit_money' => $total_money
            ], [
                'id' => $this->user['id']
            ]);
        }
        Agent::updateAllCounters([
            'balance' => $this->cashOrder['account_amount'] + $this->agent_fee
        ], [
            'id' => $this->agent['id']
        ]);
        return true;
    }
}