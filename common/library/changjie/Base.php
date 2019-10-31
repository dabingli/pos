<?php
namespace common\library\changjie;

use yii;
use yii\base\Component;
use yii\base\Model;
use yii\httpclient\Client;

/**
 * 畅捷请求初始类
 *
 * @author Administrator
 *        
 */
abstract class Base extends Model
{

    protected static $client;

    protected static $request;

    /**
     * 请求方式，可重写该属性
     *
     * @var string
     */
    public static $method = 'POST';

    /**
     * 版本号，可重新该属性
     *
     * @var string
     */
    public $Version = '1.0';

    /**
     * 请求编码
     *
     * @var string
     */
    public $InputCharset = 'UTF-8';

    /**
     * 加密方式
     *
     * @var string
     */
    const SIGNTYPE = 'RSA';

    public function init()
    {
        Yii::$app->log->targets[0]->logFile = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'changjie' . date('Ymd') . '.log';
        parent::init();
        self::$client = new Client([
            'baseUrl' => static::getBaseUrl()
        ]);
        self::$request = self::$client->createRequest();
        self::$request->setUrl(static::getUrl());
        self::$request->setMethod(static::$method);
    }

    public function http()
    {
        self::$request->on(Client::EVENT_BEFORE_SEND, [
            $this,
            'beforeSend'
        ]);
        self::$request->on(Client::EVENT_AFTER_SEND, [
            $this,
            'afterSend'
        ]);
        if (! $this->validate()) {
            return false;
        }
        $data = self::arrayFilter($this->toArray());
        return self::$request->setData($data);
    }

    /**
     * 请求地址
     */
    abstract static public function getUrl();

    /**
     * 请求的域名地址
     *
     * @return mixed
     */
    static public function getBaseUrl()
    {
        return Yii::$app->params['changjie']['baseUrl'];
    }

    public function afterSend($event)
    {
        Yii::error('返回的数据是' . var_export([
            'getUrl' => $event->request->getUrl(),
            'getContent' => $event->request->getContent()
        ], true));
    }

    /**
     * 发送之前处理
     *
     * @param unknown $event            
     */
    public function beforeSend($event)
    {
        $url = $event->request->getUrl();
        $event->request->setUrl($url);
        $data = $event->request->getData();
        $data['TradeDate'] = date('Ymd');
        $data['TradeTime'] = date('His');
        $data['PartnerId'] = Yii::$app->params['changjie']['PartnerId'];
        $data['Sign'] = $this->rsaSign($data);
        $data['SignType'] = self::SIGNTYPE;
        $event->request->setFormat(Client::FORMAT_URLENCODED);
        Yii::error('请求参数是' . var_export([
            'getUrl' => $url,
            'data' => $data
        ], true));
        $data = $event->request->setData($data);
    }

    /**
     * 过滤一些空值
     *
     * @param unknown $args            
     * @return unknown
     */
    static public function arrayFilter($args)
    {
        // $args = array_filter($args); // 能将值为0的过滤
        foreach ($args as $k => $val) {
            if ($val === '' || $val === null) {
                unset($args[$k]);
            }
        }
        return $args;
    }

    /**
     * 功能： 签名
     * author:
     * $args 签名字符串数组
     * return 签名结果
     */
    protected function rsaSign($args)
    {
        $args = self::arrayFilter($args);
        ksort($args);
        $query = '';
        foreach ($args as $k => $v) {
            if ($k == 'SignType') {
                continue;
            }
            if ($query) {
                $query .= '&' . $k . '=' . $v;
            } else {
                $query = $k . '=' . $v;
            }
        }
        $private_key = Yii::$app->params['changjie']['rsa_private_key'];
        $pkeyid = openssl_get_privatekey($private_key);
        openssl_sign($query, $sign, $pkeyid);
        openssl_free_key($pkeyid);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 功能：加密
     *
     * @param $args 加密原文数组
     *            return 密文数组
     */
    protected function publicRsaSign($args)
    {
        $public_key = Yii::$app->params['changjie']['rsa_public_key'];
        foreach ($args as $k => $v) {
            openssl_public_encrypt($v, $encryptStr, $public_key);
            $args[$k] = base64_encode($encryptStr);
        }
        return $args;
    }

    /**
     * 对单个字符串进行加密
     *
     * @param string $string            
     * @return string
     */
    protected function rsaSignOne(string $string)
    {
        $encryptStr = '';
        $public_key = Yii::$app->params['changjie']['rsa_public_key'];
        openssl_public_encrypt($string, $encryptStr, $public_key);
        return base64_encode($encryptStr);
    }

    /**
     * 功能： 验证签名
     *
     * @param $args 需要签名的数组            
     * @param $sign 签名结果
     *            return 验证是否成功
     */
    public static function rsaVerify($args, $sign)
    {
        $args = self::arrayFilter($args);
        
        ksort($args);
        $query = '';
        foreach ($args as $k => $v) {
            if ($k == 'sign_type' || $k == 'sign') {
                continue;
            }
            if ($query) {
                $query .= '&' . $k . '=' . $v;
            } else {
                $query = $k . '=' . $v;
            }
        }
        // 这地方不能用 http_build_query 否则会urlencode
        $sign = base64_decode($sign);
        $public_key = Yii::$app->params['changjie']['rsa_public_key'];
        $pkeyid = openssl_get_publickey($public_key);
        if ($pkeyid) {
            $verify = openssl_verify($query, $sign, $pkeyid);
            openssl_free_key($pkeyid);
        }
        if ($verify == 1) {
            return true;
        } else {
            return false;
        }
    }
}