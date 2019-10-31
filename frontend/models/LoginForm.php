<?php
namespace frontend\models;

use common\models\agent\Agent;
use common\models\agent\AgentUser;
use Yii;
use common\models\agent\AgentUser as User;

/**
 * 登录表单
 *
 * Class LoginForm
 *
 * @package backend\models
 * @author jianyan74 <751393839@qq.com>
 */
class LoginForm extends \common\models\common\LoginForm
{

    public $verifyCode;

    /**
     * 默认登录失败99999次显示验证码
     *
     * @var int
     */
    public $attempts = 99999;

    /**
     *
     * @var bool
     */
    public $rememberMe = false;

    public function init()
    {
        parent::init();
        $attempts = Yii::$app->debris->config('frontendAttempts');
        if ($attempts) {
            $this->attempts = 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'username',
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
            ],
            [
                'password',
                'validateIp'
            ],
            [
                'username',
                'validateExpire'
            ],
            [
                'verifyCode',
                'captcha',
                'on' => 'captchaRequired'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'rememberMe' => '记住我',
            'password' => '密码',
            'verifyCode' => '验证码'
        ];
    }

    /**
     * 验证ip地址是否正确
     *
     * @param
     *            $attribute
     * @throws \yii\base\InvalidConfigException
     */
    public function validateIp($attribute)
    {
        $ip = Yii::$app->request->userIP;
        $allowIp = Yii::$app->debris->config('sys_allow_ip');
        if (! empty($allowIp)) {
            $ipList = explode(",", $allowIp);
            if (! in_array($ip, $ipList)) {
                // 记录行为日志
                Yii::$app->services->sys->log('login', '限制IP登录', false);
                
                $this->addError($attribute, '登录失败');
            }
        }
    }

    /**
     * @param $attribute
     * 验证有效期
     */
    public function validateExpire($attribute)
    {
        $user = $this->getUser();
        $agent = Agent::findOne([
            'id' => $user->agent_id
        ]);
        if($agent->expired_time - time() <= 0)
        {
            $this->addError($attribute, '已过期，请充值');
        }
    }

    /**
     *
     * @return mixed|null|static
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        
        return $this->_user;
    }

    /**
     * 验证码显示判断
     */
    public function loginCaptchaRequired()
    {
        if (Yii::$app->session->get('loginCaptchaRequired') >= $this->attempts) {
            $this->setScenario("captchaRequired");
        }
    }

    /**
     * 登陆
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function login()
    {
        if ($this->validate() && Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)) {
            Yii::$app->session->remove('loginCaptchaRequired');
            
            return true;
        }
        $counter = Yii::$app->session->get('loginCaptchaRequired') + 1;
        Yii::$app->session->set('loginCaptchaRequired', $counter);
        return false;
    }
}
