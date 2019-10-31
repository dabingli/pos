<?php
namespace common\models\pos;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\agent\Agent;

class ActivationLog extends \common\models\common\BaseModel
{

    public static function tableName()
    {
        return '{{%activation_log}}';
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
                    'customerName',
                    'activeId',
                    'posCati',
                    'customerNo',
                    'activeTime',
                    'serviceLevel',
                    'serviceNo',
                    'sign'
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
            'customerName' => '商户名称',
            'activeId' => '激活ID',
            'posCati' => '终端号',
            'customerNo' => '商户编号',
            'activeTime' => '激活时间',
            'serviceLevel' => '服务商级别',
            'serviceNo' => '服务商编号',
            'sign' => '签名字符串',
            'created_at' => '创建时间'
        ];
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'number' => 'customerNo'
        ]);
    }
}
