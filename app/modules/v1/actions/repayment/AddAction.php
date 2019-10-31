<?php
namespace app\modules\v1\actions\repayment;

use yii;
use yii\base\Action;
use common\models\entities\BillsUserPlanOrder;
use common\models\entities\User;
use common\models\entities\Order;
use common\models\entities\UserBankCard;

/**
 * 提交还款订单
 *
 * @author Administrator
 *        
 */
class AddAction extends Action
{

    public $amount;

    public $payPassword;

    public $userBankCardId;

    protected $userBankCardModel;

    public function cardVerification()
    {
        $model = UserBankCard::find();
        $model->andWhere([
            'id' => $this->userBankCardId,
            'agent_id' => Yii::$app->params['agentModel']->id,
            'is_delete' => UserBankCard::NOT_DELETE
        ]);
        $model->with('userBankCardCcExtend');
        $model = $model->one();
        
        if (empty($model)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '银行卡不存在'
                ],
                'data' => []
            ];
        }
        if ($model->type != UserBankCard::CC_TYPE) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '该银行卡不是信用卡'
                ],
                'data' => []
            ];
        }
        if (empty($model->userBankCardCcExtend)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '信用卡信息错误'
                ],
                'data' => []
            ];
        }
        $this->userBankCardModel = $model;
        return true;
    }

    public function run()
    {
        $cardVerification = $this->cardVerification();
        if (true !== $cardVerification) {
            return $cardVerification;
        }
        if ($this->amount <= 0) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '还款金额不能少于0!'
                ],
                'data' => []
            ];
        }
        if (Yii::$app->user->identity->repayment_money < $this->amount) {
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
        $fee = Yii::$app->params['agentModel']->business->repayment_procedures_amount;
        if ($fee <= 0) {
            $fee = 0;
        }
        $model = new Order();
        $model->load([
            'agent_id' => Yii::$app->params['agentModel']->id,
            'app_user_id' => Yii::$app->params['appModel']->id,
            'user_id' => Yii::$app->user->id,
            'card_name' => Yii::$app->user->identity->user_name,
            'user_bank_card_id' => $this->userBankCardId,
            'bank_card' => $this->userBankCardModel->cardNo,
            'fee' => $fee,
            'amount' => sprintf("%.2f", $this->amount - $fee),
            'type' => Order::REPAYMENT,
            'mobile' => Yii::$app->user->identity->mobile
        ], '');
        if (! $model->save()) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '还款失败'
                ],
                'data' => []
            ];
        }
        $transaction->commit();
        return [
            'status' => 0,
            'code' => 200,
            'message' => [
                '还款成功'
            ],
            'data' => $model
        ];
    }
}