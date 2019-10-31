<?php
namespace common\library\pos;

use yii;
use yii\base\Component;
use common\library\pos\Client;
use yii\base\Model;

abstract class Base extends Model
{

    protected static $client;

    protected static $request;

    public static $method = 'POST';

    public $interfaceType = 'php';

    protected $key;

    protected $iv;

    public function init()
    {
        parent::init();
        $this->key = Yii::$app->params['pos']['key'];
        $this->iv = Yii::$app->params['pos']['iv'];
        self::$client = new Client([
            'baseUrl' => static::getBaseUrl()
        ]);
        self::$request = self::$client->createRequest();
        self::$request->setUrl(static::getUrl());
        self::$request->setMethod(static::$method);
    }

    function decrypt($data)
    {
        return openssl_decrypt($data, 'DES-CBC', $this->key, null, $this->iv);
    }

    public function http()
    {
        self::$request->on(Client::EVENT_BEFORE_SEND, [
            $this,
            'beforeSend'
        ]);
        $this->validate();
        return self::$request->setData($this->toArray());
    }

    static public function getBaseUrl()
    {
        return Yii::$app->params['pos']['posPostUrl'];
    }

    public function beforeSend($event)
    {
        $url = $event->request->getUrl();
        $event->request->setUrl($url);
        $data = $event->request->getData();
        $data['reqData'] = $this->encrypt($data);
        $data['mac'] = $this->getMac($data);
        $event->request->setFormat(Client::FORMAT_JSON);
        $data = $event->request->setData($data);
    }

    public function encrypt(array $data)
    {
        $data['channel'] = Yii::$app->params['pos']['channel'];
        $data['version'] = Yii::$app->params['pos']['version'];
        $data = json_encode($data);
        return openssl_encrypt($data, 'DES-CBC', $this->key, null, $this->iv);
    }

    public function getMac($data)
    {
        $data['channel'] = Yii::$app->params['pos']['channel'];
        $data['version'] = Yii::$app->params['pos']['version'];
        $data = json_encode($data);
        return hash_hmac('md5', $data, $this->key, false);
    }
}