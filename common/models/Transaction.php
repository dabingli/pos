<?php
namespace common\models;

use common\models\agent\Agent;
use Yii;
use yii\db\ActiveRecord;
use common\models\user\User;
use yii\behaviors\TimestampBehavior;

class Transaction extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%transaction}}';
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

    public function rules()
    {
        return [
            
            [
                [
                    'merchantId',
                    'merchantName',
                    'orderNo',
                    'txDate',
                    'agent_id',
                    'user_id',
                    'txTime',
                    'txAmt',
                    'regDate',
                    'transType',
                    'cardType',
                    'rate',
                    'amountArrives',
                    'fee',
                    'serialNo'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'agent_id',
                    'user_id',
                    'txAmt',
                    'rate',
                    'fee',
                    'amountArrives'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'merchantId' => '商户编号',
            'merchantName' => '商户名称',
            'orderNo' => '订单编号',
            'txDate' => '交易日期',
            'agent_id' => '代理商ID',
            'user_id' => '用户ID',
            'txTime' => '交易时间',
            'txAmt' => '交易金额',
            'transType' => '交易方式',
            'cardType' => '类型',
            'rate' => '费率',
            'amountArrives' => '到帐金额',
            'fee' => '手续费',
            'serialNo' => '硬件SN号'
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

    static public function TypeLabels()
    {
        return [
            'PURCHASE' => '消费',            
            'PRE_AUTH' => '预授权',
            'PRE_AUTH_COMP' => '预授权完成'
        ];
    }

    public function getType()
    {
        $typeLabels = self::TypeLabels();
        return isset($typeLabels[$this->transType]) ? $typeLabels[$this->transType] : '';
    }

    static public function CardTypeLabels()
    {
        return [
            'DEBIT_CARD' => '借记卡',
            'CREDIT_CARD' => '贷记卡',
            'PREPAID_CARD' => '预付卡',
            'SEMI_CREDIT_CARD' => '准贷记卡'
        ];
    }

    public function getCardType()
    {
        $typeLabels = self::CardTypeLabels();
        return isset($typeLabels[$this->cardType]) ? $typeLabels[$this->cardType] : '';
    }

    /**
     * @existProductNo 根据机具编号查询是否存在交易记录
     * @param string|array $nos
     * @return bool
     */
    public static function existProductNo($nos = '')
    {
        $self = self::find();
        if(is_string($nos)) {

            $self->where(['serialNo'=>$nos]);

        } else if (is_array($nos)) {

            $self->where(['in', 'serialNo', $nos]);

        } else {
            return false;
        }

        if( !($self->one()) ) {
            return false;
        }

        return true;
    }
}