<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;

class ForgetPwdForm extends Model
{

    public $code;

    public $password;

    public $repeat_password;

    public $mobile;

    protected $user;

    public function rules()
    {
        return [
            [
                [
                    'mobile',
                    'code',
                    'password',
                    'repeat_password'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'password',
                'string',
                'min' => 6,
                'max' => 30
            ],
            [
                'password',
                'validatePassword'
            ],
            [
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/user/set-up-forget_pwd-code',
                'message' => '验证码错误'
            ],
            [
                [
                    'password'
                ],
                'filter',
                'filter' => 'trim',
                'skipOnArray' => true
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'code' => '验证码',
            'password' => '登录密码',
            'repeat_password' => '重复登录密码'
        ];
    }

    public function validatePassword($attribute)
    {
        if($this->password != $this->repeat_password)
        {
            $this->addError($attribute, '两次输入的密码不一致');
            return false;
        }
    }

    public function setUpPassword()
    {
        $user = User::findOne(['mobile'=>$this->mobile]);
        if (! $this->validate()) {
            return null;
        }
        if(empty($user))
        {
            $this->addError('mobile', '账号不存在');
            return false;
        }
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        if ($user->save()) {
            return true;
        } else {
            $this->addErrors($user->getFirstErrors());
            return null;
        }
    }
}