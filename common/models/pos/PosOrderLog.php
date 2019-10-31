<?php
namespace common\models\pos;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\agent\Agent;
use common\models\user\User;
use common\models\Profit;
use common\models\user\UserSettlement;
use common\models\agent\AgentProductType;

class PosOrderLog extends \common\models\common\BaseModel
{

    public static function tableName()
    {
        return '{{%pos_order_log}}';
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

    /**
     *
     * {@inheritdoc}
     *
     */
    public function rules()
    {
        return [
            [
                [
                    'orderId',
                    'posCati',
                    'transType',
                    'cardType',
                    'amount',
                    'createTime',
                    'completeTime',
                    'orderStatus',
                    'customerNo',
                    'customerName',
                    'serviceNo',
                    'agentName',
                    'agentLevel',
                    'rate',
                    'creditRate',
                    'upperLimitFee',
                    'sign',
                    'isDoubleFree'
                ],
                'safe'
            ]
        ];
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderId' => '订单号',
            'posCati' => '终端号',
            'transType' => '交易类型',
            'cardType' => '卡类型',
            'amount' => '金额',
            'createTime' => '交易创建时间',
            'completeTime' => '交易完成时间',
            'orderStatus' => '交易状态',
            'customerNo' => '商户编号',
            'customerName' => '商户名称',
            'serviceNo' => '服务商编号',
            'agentName' => '代理商名称',
            'agentLevel' => '代理商级别',
            'rate' => '借记卡费率',
            'creditRate' => '贷记卡费率',
            'upperLimitFee' => '借记卡封顶值'
        ];
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'number' => 'customerNo'
        ]);
    }
}