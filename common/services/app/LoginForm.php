<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;
use common\models\agent\Agent;

/**
 * 登录
 *
 * @author zhouchen
 *        
 */
class LoginForm extends Model
{

    public $mobile;

    public $password;

    public $rememberMe = false;

    private $_user;

    const GET_ACCESS_TOKEN = 'generate_access_token';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->on(self::GET_ACCESS_TOKEN, [
            $this,
            'onGenerateAccessToken'
        ]);
    }

    /**
     *
     * @ERROR!!!
     *
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [
                [
                    'mobile',
                    'password'
                ],
                'required',
                'message' => '{attribute}不能为空'
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

    public function attributeLabels()
    {
        return [
            'mobile' => '登录手机',
            'password' => '登录密码'
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
            if ($user->status != User::OPEND_STATUS) {
                $this->addError($attribute, '该帐号状态不正常');
                return false;
            }
        }
        return true;
    }

    /**
     * 登录
     *
     * @return boolean
     */
    public function login()
    {
        if ($this->validate()) {
            $this->trigger(self::GET_ACCESS_TOKEN);
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 7 : 0);
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne([
                'mobile' => $this->mobile
            ]);
        }
        
        return $this->_user;
    }

    public function onGenerateAccessToken()
    {
        if (! User::validateAccessToken($this->getUser()->password_reset_token)) {
            $this->getUser()->generateAccessToken();
            $this->getUser()->save(false);
        }
    }
}