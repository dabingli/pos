<?php
namespace common\models;
use common\models\product\Product;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\user\User;
use common\models\agent\Agent;

class MerchantUser extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%merchant_user}}';
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
                    'organId',
                    'phone',
                    'serialNo',
                    'terminalId',
                    'agent_id',
                    'user_id',
                    'bindingTime'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'merchantId' => '商户编号',
            'merchantName' => '商户名称',
            'organId' => 'organId',
            'agent_id' => '代理商ID',
            'user_id' => '用户ID',
            'phone' => '商户手机号',
            'serialNo' => '机具编号',
            'terminalId' => '终端编号',
            'bindingTime' => '激活时间绑定时间'
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

    public function getTransaction()
    {
        return $this->hasMany(Transaction::className(),[
            'merchantId' => 'merchantId'
        ]);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(),[
           'product_no' => 'serialNo'
        ]);
    }
}