<?php
namespace common\models\services\backend;

use Yii;
use yii\base\Model;
use common\models\entities\AdminUser;

class LoginForm extends Model
{

    public $user_name;

    public $password;

    public $rememberMe = true;

    private $_user;

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
                    'user_name',
                    'password'
                ],
                'required'
            ],
            [
                'rememberMe',
                'boolean'
            ],
            [
                'password',
                'validatePassword'
            ]
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();
            if (! $user || ! $user->validatePassword($this->password)) {
                $this->addError($attribute, '登录密码错误');
                return false;
            }
        }
        return true;
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = AdminUser::findByUsername($this->user_name);
        }
        
        return $this->_user;
    }
}