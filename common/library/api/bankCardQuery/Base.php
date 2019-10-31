<?php
namespace common\library\api\bankCardQuery;

use Yii;
use yii\base\Model;
use yii\httpclient\Client;

abstract class Base extends Model
{

    protected $headers = [];

    protected $client;

    protected $request;

    protected $appcode;

    const CHARSET = 'UTF-8';

    const METHOD = 'GET';

    abstract protected function getBaseUrl();

    public function init()
    {
        parent::init();
        $this->setAppcode();
        $this->client = new Client([
            'baseUrl' => $this->getBaseUrl()
        ]);
        $this->request = $this->client->createRequest();
        $this->request->setMethod(self::METHOD);
        $this->request->setFormat(Client::FORMAT_URLENCODED);
        $this->setHeaders();
    }

    protected function setHeaders()
    {
        $this->request->addHeaders([
            'Authorization' => 'APPCODE ' . $this->appcode
        ]);
    }

    protected function setAppcode()
    {
        return $this->appcode = Yii::$app->params['ocridcard']['AppCode'];
    }
}