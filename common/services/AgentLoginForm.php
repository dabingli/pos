<?php
namespace common\models\services;

use yii;
use yii\base\Model;
use common\models\entities\AgentUser as User;

class AgentLoginForm extends Model
{

    public $account;

    public $password;

    public $rememberMe = false;

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
                    'account',
                    'password'
                ],
                'trim'
            ],
            [
                [
                    'account',
                    'password'
                ],
                'required'
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
            $this->_user = User::findByUsername($this->account);
        }
        
        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'account' => '登录帐号',
            'password' => '登录密码'
        ];
    }
}
