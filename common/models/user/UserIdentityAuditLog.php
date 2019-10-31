<?php
namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


class UserIdentityAuditLog extends ActiveRecord
{

    const AUDIT = 1;

    const FAIL = 1;

    const SUCCESS = 2;

    const IDENTITY = 1;


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

    public static function getTypeLabels()
    {
        return [
            self::IDENTITY => '身份证',
        ];
    }

    public function getType(){
        $type = self::getTypeLabels();
        return $type[$this->type];
    }

    public static function getStatusLabels()
    {
        return [
            self::FAIL => '失败',
            self::SUCCESS => '成功',
        ];
    }

    public function getStatus(){
        $status = self::getStatusLabels();
        return $status[$this->status];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->order_sn = date('YmdHis') . rand(10000, 99999);
        }
        return parent::beforeSave($insert);
    }

    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'order_sn',
                    'created_at',
                    'status',
                    'type',
                    'mobile',
                    'description',
                    'agent_id'
                ],
                'safe'
            ],
            [
                [
                    'cardNo',
                    'mobile',
                    'identity_card',
                    'real_name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
        ];
    }

    public function attributeLabels()
    {
       return [
         'user_id' => '用户id',
         'order_sn' => '订单号',
           'status' => '审核状态',
           'mobile' => '手机号',
           'description' => '审核描述',
           'created_at' => '认证时间',
           'code' => '验证码'
       ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),[
            'id' => 'user_id'
        ]);
    }

//    public function getUserIdentityAudit()
//    {
//        return $this->hasOne(UserIdentityAudit::className(),[
//            'id' => 'identity_audit_id'
//        ]);
//    }
}