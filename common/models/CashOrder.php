<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\user\User;
use yii\helpers\Url;
use common\components\queue\job\OnlineWithdraw;
use common\library\changjie\PrePay;
use common\models\user\BankCard;
use common\models\agent\Agent;

class CashOrder extends ActiveRecord
{

    const RETURN_CASH = 1;

    const PROFIT = 2;

    const HANDLE = 0;

    const SUCCESS = 1;

    const FAIL = 2;

    const TRANSACTION_DISTRIBUTION = 3;

    const FROZEN_DISTRIBUTION = 4;

    const AUDIT_WAIT = 0;

    const AUDIT_SUCCESS = 1;

    const AUDIT_FAIL = 2;

    public $co_cardNo;

    public $co_bank;

    public $co_money;

    public $co_real_name;

    public $co_OutTradeNo;

    public $co_bankName;

    public $co_cash_amount;

    public static function tableName()
    {
        return '{{%cash_order}}';
    }

    public static function typeLabels()
    {
        return [
            self::RETURN_CASH => '返现提现',
            self::PROFIT => '分润提现'
        ];
    }

    public static function handleLabels()
    {
        return [
            self::AUDIT_WAIT => '待审核',
            self::AUDIT_SUCCESS => '审核通过',
            self::AUDIT_FAIL => '审核不通过'
        ];
    }

    public static function statusLabels()
    {
        return [
            self::HANDLE => '处理中',
            self::SUCCESS => '成功',
            self::FAIL => '失败'
        ];
    }

    public function getType()
    {
        $typeLabels = self::typeLabels();
        return isset($typeLabels[$this->type]) ? $typeLabels[$this->type] : '';
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    public function getHandle()
    {
        $handleLabels = self::handleLabels();
        return isset($handleLabels[$this->handle]) ? $handleLabels[$this->handle] : '';
    }

    public function rules()
    {
        return [
            [
                'type',
                'in',
                'range' => array_keys(self::typeLabels())
            ],
            [
                'status',
                'in',
                'range' => array_keys(self::statusLabels())
            ],
            [
                [
                    'user_id',
                    'mobile',
                    'type',
                    'agent_id',
                    'cash_amount',
                    'fee',
                    'account_amount',
                    'cash_provider',
                    'status',
                    'unique_order',
                    'bank',
                    'cardNo'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'fee',
                    'account_amount',
                    'cash_amount',
                    'agent_fee'
                ],
                'double'
            ],
            [
                [
                    'agent_id',
                    'user_id',
                    'cash_amount',
                    'fee',
                    'account_amount'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ],
            [
                'remarks',
                'default',
                'value' => ''
            ],
            [
                [
                    'created_at',
                    'handle',
                    'order'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'order' => '提现订单号',
            'agent_id' => '商家ID',
            'user_id' => '用户ID',
            'mobile' => '提现的手机号码',
            'type' => '提现类型',
            'cash_amount' => '提现金额',
            'fee' => '手续费',
            'account_amount' => '到帐金额',
            'cash_provider' => '提现人',
            'status' => '交易状态',
            'remarks' => '备注',
            'bank' => '银行简称',
            'cardNo' => '银行卡号'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'id' => 'agent_id'
        ]);
    }

    // public function beforeSave($insert)
    // {
    // if ($insert) {
    // $this->order = date('YmdHis') . rand(10000, 99999);
    // }
    // return parent::beforeSave($insert);
    // }
    public function getBankCard()
    {
        return $this->hasOne(BankCard::className(), [
            'bank' => 'bank'
        ]);
    }

    public function Withdraw()
    {
        $CorpPushUrl = Url::toRoute([
            '/chang-jie-async/online-withdraw'
        ], true);

        $model = new \common\library\changjie\QueryBalance();
        $model->load([
            'OutTradeNo' => time()
        ], '');
        $http = $model->http();
        $content= $http->send()->content;
        $content = json_decode($content, true);

        if($content['PayBalance'] < $this->co_cash_amount)
        {
            $cashOrder = self::findOne(['order'=>$this->co_OutTradeNo]);
            $cashOrder->remarks = '余额不足';
            $cashOrder->status = self::FAIL;
            $cashOrder->save();

            $order = Order::findOne(['order'=>$this->co_OutTradeNo]);
            $order->status = self::FAIL;
            $order->save();
        }

        $PrePay = new PrePay();
        $PrePay->load([
            'AcctNo' => $this->co_cardNo,
            'BusinessType' => 0,
            'BankCode' => $this->co_bank,
            'TransAmt' => $this->co_money,
            'AcctName' => $this->co_real_name,
            'OutTradeNo' => $this->co_OutTradeNo,
            'BankCommonName' => $this->co_bankName,
            'BranchBankName' => '中国建设银行广州东山广场分理处',
            'CorpPushUrl' => $CorpPushUrl,
            'AccountType' => '00'
        ], '');
        $http = $PrePay->http();
        $content = $http->send()->content;

        $content = json_decode($content, true);
        
        if($content['PlatformRetCode'] == '0000'){
            if (isset($content['AppRetcode']) && $content['AppRetcode'] == '01019999') {
                Yii::$app->queue->delay(15 * 60)->push(new OnlineWithdraw([
                    'outerTradeNo' => $this->co_OutTradeNo
                ]));
            }
        }else{
            $cashOrder = self::findOne(['order'=>$this->co_OutTradeNo]);
            $cashOrder->remarks = $content['PlatformErrorMessage'];
            $cashOrder->status = self::FAIL;
            $cashOrder->save();

            $order = Order::findOne(['order'=>$this->co_OutTradeNo]);
            $order->status = self::FAIL;
            $order->save();

            if($cashOrder['type'] == self::RETURN_CASH)
            {
                User::updateAllCounters([
                    'activate_money' => $cashOrder['cash_amount']
                ], [
                    'id' => $cashOrder['user_id']
                ]);
            }else {
                User::updateAllCounters([
                    'profit_money' => $cashOrder['cash_amount']
                ], [
                    'id' => $cashOrder['user_id']
                ]);
            }
            $agent = Agent::findOne(['id' => $cashOrder['agent_id']]);
            Agent::updateAllCounters([
                'balance' => $cashOrder['account_amount'] + $agent->agent_fee
            ], [
                'id' => $cashOrder['agent_id']
            ]);
        }
    }

}