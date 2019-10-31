<?php
namespace app\models;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use common\models\user\User;

class Authenticator
{

    public $cache;

    const USER_NAME = 'user';

    public $authTimeout = 3600;

    public $header = 'Authorization';

    /**
     *
     * {@inheritdoc}
     *
     */
    public $pattern = '/^Bearer\s+(.*?)$/';

    protected $authHeader;

    public function __construct()
    {
        $this->cache = Yii::$app->cache;
        $this->authHeader();
    }

    /**
     * 判断token是否存在
     *
     * @return boolean|unknown|string|array
     */
    public function authHeader()
    {
        $authHeader = Yii::$app->request->getHeaders()->get($this->header);
        if ($this->pattern !== null) {
            if (preg_match($this->pattern, $authHeader, $matches)) {
                $authHeader = $matches[1];
            } else {
                return false;
            }
        }
        $this->authHeader = $authHeader;
        
        return $this->authHeader;
    }

    public function isAuth()
    {
        if (false === $this->authHeader) {
            return false;
        }
        $res = $this->cache->get($this->authHeader);
        if (false === $res) {
            return false;
        }
        return $this->cache->set($this->authHeader, $res);
    }

    public function set($name, $value)
    {
        $authHeader = $this->authHeader;
        if (empty($authHeader)) {
            return false;
        }
        $res = $this->cache->get($authHeader);
        $res[$name] = $value;
        if ($this->authTimeout === false) {
            $this->cache->set($authHeader, $res);
        } else {
            $this->cache->set($authHeader, $res, $this->authTimeout);
        }
        
        return true;
    }

    public function get($name)
    {
        $authHeader = $this->authHeader;
        
        if (empty($authHeader)) {
            return false;
        }
        $res = $this->cache->get($authHeader);
        $this->cache->set($authHeader, $res, $this->authTimeout);
        return isset($res[$name]) ? $res[$name] : null;
    }

    public function getAccessToken()
    {
        $name = Yii::$app->security->generateRandomString(100) . date('YmdHis');
        $this->cache->set($name, []);
        return $name;
    }

    /**
     * 用户登录的时候保存用户信息
     *
     * @param User $user            
     */
    public function setUser(User $user)
    {
        return Yii::$app->authenticator->set(self::USER_NAME, $user->id);
    }

    public function getUser()
    {
        return Yii::$app->authenticator->get(self::USER_NAME);
    }

    /**
     * 用户退出的时候清空
     */
    public function clearUser()
    {
        return $this->remove(self::USER_NAME);
    }

    /**
     * 清除一个值
     *
     * @param unknown $name            
     * @return boolean
     */
    public function remove($name)
    {
        $authHeader = $this->authHeader;
        if (empty($authHeader)) {
            return false;
        }
        $res = $this->cache->get($authHeader);
        if (isset($res[$name])) {
            unset($res[$name]);
        }
        if ($this->authTimeout === false) {
            $this->cache->set($authHeader, $res);
        } else {
            $this->cache->set($authHeader, $res, $this->authTimeout);
        }
    }
}
