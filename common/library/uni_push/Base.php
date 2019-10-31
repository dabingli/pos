<?php
namespace common\library\uni_push;

use yii;
use yii\base\Model;

/**
 * uni_push请求初始类
 *
 * @author Administrator
 *        
 */
abstract class Base extends Model
{

    protected $appId;

    protected $appKey;

    protected $appSecret;

    protected $masterSecret;

    public $host;

    public function init()
    {
        parent::init();

        $this->appId = Yii::$app->params['uniPush']['appId'];
        $this->appKey = Yii::$app->params['uniPush']['appKey'];
        $this->appSecret = Yii::$app->params['uniPush']['appSecret'];
        $this->masterSecret = Yii::$app->params['uniPush']['masterSecret'];

    }


    /**
     * 请求地址
     */
    abstract public function send();


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
        return Yii::$app->params['uniPush']['baseUrl'];
    }


}