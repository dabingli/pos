<?php
namespace common\models\product;

use common\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\AgentProductType;

class ProductLog extends ActiveRecord
{

    const IN_STORE = 1;

    const SEND = 2;

    const REFUND = 3;

    const NO_SEND = 4;

    public function rules()
    {
        return [
            [
                [
                    'id',
                    'name',
                    'store_time',
                    'expire_time',
                    'refund_time',
                    'back_time',
                    'status',
                    'product_no',
                    'send_time',
                    'model',
                    'user_id',
                    'agent_id',
                    'serial',
                    'agent_product_type_id',
                    'user_name',
                    'user_code',
                    'mobile'
                ],
                'safe',
            ],
//            [
//                'total',
//                'number',
//                'min'=>1,
//                'message'=>'{attribute}不能少于1'
//            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'name' => '入库人员',
            'store_time' => '入库时间',
            'expire_time' => '到期时间',
            'refund_time' => '退货时间',
            'back_time' => '回拨时间',
            'status' => '状态',
            'product_no' => '机具编号',
            'send_time' => '下发时间'
        ];
    }

    public function getAgentProductType()
    {
        return $this->hasOne(AgentProductType::className(), [
            'id' => 'agent_product_type_id'
        ]);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), [
           'product_no' => 'product_no'
        ]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    public function getNew()
    {
        return $this->hasOne(User::className(), [
            'user_code' => 'user_code'
        ]);
    }

    public static function getSerial($type)
    {
        switch($type)
        {
            case self::IN_STORE;
                $prefix = 'RK';
                break;
            case self::SEND;
                $prefix = 'XF';
                break;
            case self::REFUND;
                $prefix = 'TH';
                break;
            case self::NO_SEND;
                $prefix = 'HB';
                break;
        }
        return $prefix . date('Ymd') . rand(1000, 9999);
    }
}