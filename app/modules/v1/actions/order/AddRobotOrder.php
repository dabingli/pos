<?php
namespace app\modules\v1\actions\order;

use yii;
use yii\base\Action;
use common\library\automatic\perfect\RepaymentRobot;
use common\models\entities\UserBankCard;
use common\models\entities\BillsUserPlan;
use common\models\entities\BillsUserPlanOrder;

class AddRobotOrder extends Action
{

    public $datas;

    public $userBankCardId;

    public $type;

    public $templateId = 1;

    protected $billsUserPlanModel;

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
        $this->datas = json_decode($this->datas, true);
        $amount = array_sum(array_column(array_column($this->datas, 'repayment'), 'amount'));
        if ($amount <= 0) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '还款总金额不能少于0'
                ],
                'data' => []
            ];
        }
        if (count($this->datas) <= 0) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '还款笔数不能少于0'
                ],
                'data' => []
            ];
        }
        $cardVerification = $this->cardVerification();
        if (true !== $cardVerification) {
            return $cardVerification;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        $this->billsUserPlanModel = new BillsUserPlan();
        $this->billsUserPlanModel->load([
            'templateId' => $this->templateId,
            'user_id' => Yii::$app->user->id,
            'app_user_id' => Yii::$app->params['appModel']->id,
            'agent_id' => Yii::$app->params['agentModel']->id,
            'user_bank_card_id' => $this->userBankCardId,
            'num' => count($this->datas),
            'amount' => $amount,
            'type' => $this->type
        ], '');
        
        if (! $this->billsUserPlanModel->save()) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => $this->billsUserPlanModel->getFirstErrors(),
                'data' => []
            ];
        }
        foreach ($this->datas as $key => $value) {
            if (array_sum(array_column($value['consumption'], 'amount')) != $value['repayment']['amount']) {
                return [
                    'status' => 0,
                    'code' => 0,
                    'message' => [
                        '还款金额与消费金额不相等'
                    ],
                    'data' => []
                ];
            }
            foreach ($value['consumption'] as $k1 => $v1) {
                $billsUserPlanOrderModel = new BillsUserPlanOrder();
                $billsUserPlanOrderModel->load([
                    'card_name' => Yii::$app->user->identity->user_name,
                    'mobile' => Yii::$app->user->identity->mobile,
                    'bank_card' => $this->userBankCardModel->cardNo,
                    'user_bank_card_id' => $this->userBankCardId,
                    'bills_user_plan_id' => $this->billsUserPlanModel->id,
                    'app_user_id' => Yii::$app->params['appModel']->id,
                    'agent_id' => Yii::$app->params['agentModel']->id,
                    'user_id' => Yii::$app->user->id,
                    'type' => BillsUserPlanOrder::CONSUME,
                    'region_id' => $value['region_id'],
                    'fee' => sprintf("%.2f", $this->billsUserPlanModel->getPlanType()
                        ->feeAmount(Yii::$app->user->identity, $v1['amount']) - $v1['amount']),
                    'amount' => $v1['amount'],
                    'actual_amount' => $v1['amount'],
                    'planned_time' => strtotime($v1['date'])
                ], '');
                
                if ($this->billsUserPlanModel->getPlanType()->getOrderMaxAmount() < $v1['amount']) {
                    $transaction->rollBack();
                    return [
                        'status' => 0,
                        'code' => 100,
                        'message' => [
                            '单笔消费金额不能超过' . $this->billsUserPlanModel->getPlanType()->getOrderMaxAmount()
                        ],
                        'data' => []
                    ];
                }
                if (! $billsUserPlanOrderModel->save()) {
                    $transaction->rollBack();
                    return [
                        'status' => 0,
                        'code' => 0,
                        'message' => $billsUserPlanOrderModel->getFirstErrors(),
                        'data' => []
                    ];
                }
            }
            
            $billsUserPlanOrderModel = new BillsUserPlanOrder();
            $billsUserPlanOrderModel->load([
                'card_name' => Yii::$app->user->identity->user_name,
                'mobile' => Yii::$app->user->identity->mobile,
                'bank_card' => $this->userBankCardModel->cardNo,
                'user_bank_card_id' => $this->userBankCardId,
                'bills_user_plan_id' => $this->billsUserPlanModel->id,
                'app_user_id' => Yii::$app->params['appModel']->id,
                'agent_id' => Yii::$app->params['agentModel']->id,
                'user_id' => Yii::$app->user->id,
                'type' => BillsUserPlanOrder::EPAYMENT,
                'region_id' => $value['region_id'],
                'fee' => Yii::$app->params['agentModel']->business->repayment_procedures_amount,
                'amount' => $value['repayment']['amount'],
                'actual_amount' => $value['repayment']['amount'],
                'planned_time' => strtotime($value['repayment']['date'])
            ], '');
            if (! $billsUserPlanOrderModel->save()) {
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'code' => 0,
                    'message' => $billsUserPlanOrderModel->getFirstErrors(),
                    'data' => []
                ];
            }
        }
        $order = BillsUserPlanOrder::find()->andWhere([
            'bills_user_plan_id' => $this->billsUserPlanModel->id
        ])
            ->orderBy([
            'id' => SORT_ASC
        ])
            ->one();
        $order->actual_amount = BillsUserPlanOrder::find()->andWhere([
            'bills_user_plan_id' => $this->billsUserPlanModel->id
        ])->sum('fee') + $order->actual_amount;
        if ($this->billsUserPlanModel->getPlanType()->getOrderMaxAmount() < $order->actual_amount) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 100,
                'message' => [
                    '单笔消费金额不能超过' . $this->billsUserPlanModel->getPlanType()->getOrderMaxAmount()
                ],
                'data' => []
            ];
        }
        if (! $order->save()) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'code' => 0,
                'message' => $order->getFirstErrors(),
                'data' => []
            ];
        }
        $transaction->commit();
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $this->billsUserPlanModel
        ];
    }
}