<?php
namespace common\models\agent;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class AgentFrozenLog extends ActiveRecord
{


    public static function tableName()
    {
        return '{{%agent_frozen_log}}';
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
                    'agent_id',
                    'user_id',
                    'user_name',
                    'user_code',
                    'product_id',
                    'product_no',
                    'model',
                    'expire_at',
                    'frozen_money',
                    'type_id',
                    'type_name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'mobile',
                ],
                'safe'
            ]
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agent_id' => '代理商id',
            'user_id' => '商户id',
            'user_name' => '商户名称',
            'user_code' => '商户编号',
            'mobile' => '商户手机',
            'product_id' => '机具id',
            'product_no' => '机型编号',
            'type_id' => '机具类型id',
            'type_name' => '机具类型名称',
            'model' => '机具型号',
            'expire_at' => '到期日期',
            'frozen_money' => '冻结金额',
            'created_at' => '冻结时间',
        ];
    }
}