<?php
namespace common\models\user;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class SmsCode extends ActiveRecord
{

    const SUCCESS = 1;

    const FAIL = 0;

    const VERIFY = 1;

    const NOT_VERIFY = 0;

    const REGISTER = 1;

    const LOGIN = 2;

    const PAY_PWD = 3;

    const BIND_CARD = 4;

    const FORGET_PWD = 5;

    const AUTH = 6;

    const WARNING_SMS = 7;

    const WARNING_REAL_NAME_TIMES = 8;

    const BALANCE = 9;

    /**
     *
     * {@inheritdoc}
     *
     */
    public static function tableName()
    {
        return '{{%sms_code}}';
    }

    public static function statusLabels()
    {
        return [
            self::SUCCESS => '成功',
            self::FAIL => '失败'
        ];
    }

    public static function typeLabels()
    {
        return [
            self::REGISTER => '注册',
            self::LOGIN => '登录',
            self::PAY_PWD => '支付密码',
            self::BIND_CARD => '绑定银行卡',
            self::FORGET_PWD => '忘记密码',
            self::AUTH => '实名',
            self::WARNING_SMS => '短信不足提醒',
            self::WARNING_REAL_NAME_TIMES => '认证次数不足提醒',
            self::BALANCE =>'代付金余额不足提醒'
        ];
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
                    'content',
                    'return_data',
                    'mobile',
                    'code'
                ],
                'string'
            ],
            [
                [
                    'user_id',
                    'agent_id',
                    'status',
                    'type',
                    'verify'
                ],
                'integer'
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
            'user_id' => '用户ID',
            'return_data' => '接口返回状态',
            'content' => '发送内容',
            'status' => '状态',
            'type' => '类型',
            'code' => '验证码',
            'verify' => '是否验证',
            'mobile' => '手机号'
        ];
    }

    public function getStatus()
    {
        $status = self::statusLabels();
        return $status[$this->status];
    }

    public function getType()
    {
        $type = self::typeLabels();
        return $type[$this->type];
    }
}