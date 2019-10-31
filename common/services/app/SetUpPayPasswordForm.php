<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;

class SetUpPayPasswordForm extends Model
{

    public $code;

    public $password;

    public function rules()
    {
        return [
            [
                [
                    'code',
                    'password'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/user/set-up-pay-password-code',
                'message' => '验证码错误'
            ],
            [
                [
                    'password'
                ],
                'match',
                'pattern' => '|^[0-9]{6}$|',
                'message' => '支付密码是由6位数字组成'
            ],
            [
                [
                    'password',
                    'code'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => '验证码',
            'password' => '支付密码'
        ];
    }

    public function setUpPayPassword(User $user)
    {
        if (! $this->validate()) {
            return null;
        }
        $user->pay_password = Yii::$app->security->generatePasswordHash($this->password);
        if ($user->save()) {
            return true;
        } else {
            $this->addErrors($user->getFirstErrors());
            return null;
        }
    }
}