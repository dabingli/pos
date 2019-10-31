<?php
namespace common\models\agent;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class AgentRechargeLog extends ActiveRecord
{

    const WAIT_PAY = 1;

    const SUCCESS = 2;

    const CLOSE = 3;

    const PAYMENT = 1;

    const SMS = 2;

    const REAL_NAME = 3;

    const DELETED = 1;

    const NORMAL = 0;

    public static function tableName()
    {
        return '{{%agent_recharge_log}}';
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }

    public static function typeLabels()
    {
        return [
            self::PAYMENT => '代付金',
            self::SMS => '短信',
            self::REAL_NAME => '实名认证',
        ];
    }

    public function getType()
    {
        $typeLabels = self::typeLabels();
        return isset($typeLabels[$this->type]) ? $typeLabels[$this->type] : '';
    }

    public function rules()
    {
        return [
            [
                [
                    'agent_id',
                    'recharge_no',
                    'app_id',
                    'type',
                    'money',
                    'title',
                    'sms_number',
                    'old_sms_number',
                    'new_sms_number',
                    'real_name_auth_number',
                    'old_real_name_auth_number',
                    'new_real_name_auth_number',
                    'old_money',
                    'new_money'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'recharge_no',
                    'trade_no'
                ],
                'unique'
            ],
            [
                [
                    'status',
                    'content',
                    'trade_no',
                    'out_biz_no',
                    'buyer_id',
                    'seller_id',
                    'pay_money',
                    'notify_at',
                    'pay_at',
                    'close_at',
                    'refund_at'
                ],
                'safe'
            ],
            [
                'status',
                'default',
                'value' => self::WAIT_PAY
            ],
            [
                'status',
                'in',
                'range' => array_keys(self::statusLabels())
            ],
            [
                'type',
                'in',
                'range' => array_keys(self::typeLabels())
            ]
        ];
    }

    public static function statusLabels()
    {
        return [
            self::WAIT_PAY => '待审核',
            self::SUCCESS => '交易成功',
            self::CLOSE => '交易关闭'
        ];
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    /**
     * @createRechargeNo 创建充值记录号
     *
     * @param
     *            $time
     * @return string
     */
    public function createRechargeNo()
    {
        return 'RCN' . time() . mt_rand(1010, 9980) . mt_rand(1001, 9980);
    }

    /**
     * @getRechargeLogByRechargeNo 根据充值单号查询充值记录
     *
     * @param
     *            $rechargeNo
     * @return AgentRechargeLog|null
     */
    public function getRechargeLogByRechargeNo($rechargeNo)
    {
        return $this->findOne([
            'recharge_no' => $rechargeNo
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agent_id' => '代理商id',
            'recharge_no' => '充值记录号',
            'trade_no' => '第三方流水号',
            'app_id' => '商户号',
            'out_biz_no' => '退款流水号',
            'buyer_id' => '买家号',
            'seller_id' => '卖家号',
            'type' => '充值类型',
            'sms_number' => '充值短信条数',
            'old_sms_number' => '充值前短信条数',
            'new_sms_number' => '充值后短信条数',
            'old_money' => '充值前余额',
            'new_money' => '充值后余额',
            'money' => '充值金额',
            'pay_money' => '支付金额',
            'refund_money' => '退款金额',
            'title' => '标题',
            'content' => '描述',
            'status' => '状态',
            'notify_at' => '异步通知时间',
            'pay_at' => '支付时间',
            'created_at' => '创建订单时间',
            'close_at' => '关闭时间',
            'refund_at' => '退款时间'
        ];
    }
}