<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;

class SetUpPasswordForm extends Model
{

    public $code;

    public $password;

    public $oldPassword;

    protected $user;

    public function rules()
    {
        return [
            [
                [
                    'code',
                    'password',
                    'oldPassword'
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
                'oldPassword',
                'validatePassword'
            ],
            [
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/user/set-up-password-code',
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
            'code' => '验证码',
            'password' => '登录密码'
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->user;
            if (! $user || ! $user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, '旧密码错误');
                return false;
            }
        }
        
        return true;
    }

    public function setUpPassword(User $user)
    {
        $this->user = $user;
        if (! $this->validate()) {
            return null;
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