<?php
namespace common\components;

use yii;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

class Sms
{

    /*
     * 短信发送
     */

    public function send($mobile, $msgContent)
    {
//        $url = Yii::$app->params['sms']['baseUrl'];
        $url = Yii::$app->params['sms']['baseUrl'];
        $account = Yii::$app->params['sms']['account'];
        $privateKey = Yii::$app->params['sms']['privateKey'];
        $client = new Client([
            'baseUrl' => $url,
            'requestConfig' => [
                'format' => Client::FORMAT_URLENCODED
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_URLENCODED
            ]
        ]);

        $time = date('YmdHis');

        $sign = array($account, $time, $privateKey);
        sort($sign);
        $sign = implode($sign, '');

        $data = [
            'nonce' => base64_encode($account.','.$time),
            'mobiles' => $mobile,
            'sendContent' => urlencode($msgContent),
            'signature' => sha1($sign),
        ];
//
        $data = json_encode($data,true);
//        $data = http_build_query($data, '&');
        
        $request = $client->post($url, $data, [
            'Content-type' => 'application/x-www-form-urlencoded'
        ]);
        
        $response = $request->send();
        
        return $response->getContent();
    }
}