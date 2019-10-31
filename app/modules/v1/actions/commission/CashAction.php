<?php
namespace app\modules\v1\actions\commission;

use yii;
use yii\base\Action;
use common\models\entities\nestedSets\UserLink;
use common\models\entities\User;
use common\models\entities\Order;
use common\models\entities\UserBankCard;
use Channel\PHPSDK;

/**
 * 子用户，第一级
 *
 * @author Administrator
 *        
 */
class CashAction extends Action
{

    public $amount;

    public $payPassword;

    public $userBankCardId;

    public function run()
    {
        if ($this->amount <= 0) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '提现金额不能少于0!'
                ],
                'data' => []
            ];
        }
        if (Yii::$app->user->identity->wallet_money < $this->amount) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '余额不足!'
                ],
                'data' => []
            ];
        }
        if (empty(Yii::$app->user->identity->pay_password)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '请设置交易密码'
                ],
                'data' => []
            ];
        }
        if (! Yii::$app->security->validatePassword($this->payPassword, Yii::$app->user->identity->pay_password)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '支付密码错误'
                ],
                'data' => []
            ];
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        
        $sql = "UPDATE " . (new User())->tableName() . " SET wallet_money=wallet_money-{$this->amount} WHERE id=" . Yii::$app->user->id . " AND wallet_money>={$this->amount}";
        if (! $db->createCommand($sql)->query()) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '提现失败'
                ],
                'data' => []
            ];
        }
        $fee = Yii::$app->user->identity->userRank->cash_fee;
        if ($fee <= 0) {
            $fee = 0;
        }
        $userBankCard = UserBankCard::findOne([
            'id' => $this->userBankCardId,
            'user_id' => Yii::$app->user->id
        ]);
        if (empty($userBankCard)) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '银行卡不存在'
                ],
                'data' => []
            ];
        }
        
        $model = new Order();
        
        $amount = sprintf("%.2f", $this->amount - $fee);
        $model->load([
            'agent_id' => Yii::$app->params['agentModel']->id,
            'app_user_id' => Yii::$app->params['appModel']->id,
            'user_id' => Yii::$app->user->id,
            'card_name' => Yii::$app->user->identity->user_name,
            'user_bank_card_id' => $this->userBankCardId,
            'bank_card' => $userBankCard->cardNo,
            'fee' => $fee,
            'amount' => $amount,
            'type' => Order::CASH,
            'mobile' => Yii::$app->user->identity->mobile
        ], '');
        if (! $model->save()) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '提现失败'
                ],
                'data' => []
            ];
        }
        $PHPSDK = new PHPSDK(Yii::$app->user->identity, Yii::$app->params['appModel']);
        $PHPSDK->acctPay($userBankCard, $model->order_sn, $this->amount, 50, '', 'http://39.108.64.148/actionQpayQuickpass.php');
        // $transaction->commit();
        return [
            'status' => 0,
            'code' => 200,
            'message' => [
                '提现成功'
            ],
            'data' => $model
        ];
    }
}