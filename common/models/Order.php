<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\user\User;

class Order extends ActiveRecord
{

    const RETURN_CASH = 1;

    const PROFIT = 2;

    const ACTIVATION_RETURN = 3;

    const FROZEN_RETURN = 4;

    const TRANSACTION_DISTRIBUTION = 5;

    const FROZEN_DISTRIBUTION = 6;

    // 满返奖励
    const RETURN_REWARDS = 7;

    // 到期冻结款
    const FROZEN_REWARDS = 8;

    const BENEFIT_RETURN_CASH = 4;

    const SUCCESS = 1;

    const FAIL = 2;

    const HANDLE = 0;

    const YES = 1;

    const NO = 2;

    const WEEK = 1;

    const MONTH = 2;

    public static function tableName()
    {
        return '{{%order}}';
    }

    public static function typeLabels()
    {
        return [
            self::RETURN_CASH => '返现提现',
            self::PROFIT => '分润提现',
            self::ACTIVATION_RETURN => '激活返现',
            self::FROZEN_RETURN => '冻结返现',
            self::TRANSACTION_DISTRIBUTION => '交易分润',
            self::FROZEN_DISTRIBUTION => '冻结分润',
            self::RETURN_REWARDS => '达标奖励',
            self::FROZEN_REWARDS => '冻结扣款',
        ];
    }

    public static function entryLabels()
    {
        return [
            self::NO => '未入账',
            self::YES=> '已入账'
        ];
    }

    public function getEntry()
    {
        $entryLabels = self::entryLabels();
        return isset($entryLabels[$this->entry]) ? $entryLabels[$this->entry] : '';
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
                    'type',
                    'agent_id',
                    'amount',
                    'entry',
                    'status'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'order',
                    'created_at',
                    'unique_order'
                ],
                'safe',

            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'order' => '订单号',
            'agent_id' => '商家ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'amount' => '金额',
            'status' => '交易状态',
            'remarks' => '备注'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    /**
     * @formatProfit 格式化收益金额， 返回两位小数的收益金额
     * @param string $profit
     * @param int $digit
     * @return null|string
     */
    public static function formatProfit($profit='0.00000000', $digit=2)
    {
        if ( empty($profit) || $digit < 1 ) return '0.00';

        $profitArray = explode('.', $profit);
        if ( empty($profitArray) ) return '0.00';

        if ( empty($profitArray[1]) ) return $profitArray[0].'.'.str_repeat('0', $digit);

        $str = strlen($profitArray[1]) < $digit ? str_repeat('0', $digit - strlen($profitArray[1])) : '';

        return $profitArray[0].'.'.substr($profitArray[1], 0, $digit) . $str;
    }

    /**
     * @addOrder 分润收益添加订单记录
     * @param $profit
     * @param int $payType
     * @return array|bool
     */
    public static function addOrder($profit, $payType=0)
    {
        $type = 0;
        switch ($profit['type']) {
            case Profit::ACTIVATION_RETURN :
                $type = self::ACTIVATION_RETURN;
                break;
            case Profit::FROZEN_RETURN :
                $type = self::FROZEN_RETURN;
                break;
            case Profit::TRANSACTION_DISTRIBUTION :
                $type = self::TRANSACTION_DISTRIBUTION;
                break;
            case Profit::FROZEN_DISTRIBUTION :
                $type = self::FROZEN_DISTRIBUTION;
                break;
            case Profit::RETURN_REWARDS :
                $type = self::RETURN_REWARDS;
                break;
        }

        $order = new Order();
        $order->load([
            'user_id' => $profit['user_id'],
            'agent_id' => $profit['agent_id'],
            'type' => $type,
            'amount' => $profit['amount_profit'],
            'status' => 0,
            'entry' => $profit['entry'],
            'created_at' => time(),
            'updated_at' => time(),
            'order' => $profit['order'],
            'unique_order' => $profit['unique_order'],
            'pay_type' => $payType,
        ], '');

        if ( $order->save() ) {
            return true;
        } else {
            return $order->getFirstErrors();
        }
    }

//    public function beforeSave($insert)
//    {
//        if ($insert) {
//            $this->order = date('YmdHis') . rand(10000, 99999);
//        }
//        return parent::beforeSave($insert);
//    }

//获取日期
    public static function getDate($type)
    {
        if($type == self::WEEK)
        {
            for($i=0;$i<7;$i++)
            {
                $t = -6 + $i;
                $created_start = date('m-d',strtotime( $t . "days"));
                if(date('m-d') == $created_start)
                {
                    $date[$i] = '今天';
                }
                else{
                    $date[$i] = $created_start;
                }
            }
        }else{
            $month = date('m',strtotime('-1 months'));
            for($i=0;$i<6;$i++)
            {
                if($i ==5)
                {
                    $date[$i] = '本月';
                }else{
                    $date[$i] = '0' . intval(--$month);
                }
//                $t = -5 + $i;
//                $created_start = date('m',strtotime( $t . "months"));
//                if(date('m') == $created_start)
//                {
//                    $date[$i] = '本月';
//                }
//                else{
//                    $date[$i] = $created_start;
//                }
            }
        }
        return $date;
    }
}