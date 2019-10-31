<?php
namespace app\components\captcha;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\Response;

class CaptchaAction extends \yii\captcha\CaptchaAction
{

    public $authenName = 'captcha:app';

    public function getVerifyCode($regenerate = false)
    {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }
        
        return $this->generateVerifyCode();
    }

    public function run()
    {
        if (Yii::$app->request->getQueryParam(self::REFRESH_GET_VAR) !== null) {
            // AJAX request for regenerating code
            $code = $this->getVerifyCode(true);
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->authenticator->set($this->authenName, $code);
            return [
                'status' => 0,
                'code' => 200,
                'message' => [
                    '验证码获取成功'
                ],
                'data' => [
                    'url' => Url::to([
                        $this->id,
                        'v' => uniqid('', true)
                    ])
                ]
            ];
        }
        
        $this->setHttpHeaders();
        Yii::$app->response->format = Response::FORMAT_RAW;
        
        return $this->renderImage($this->getVerifyCode(true));
    }

    public function validate($input, $caseSensitive)
    {
        $code = Yii::$app->authenticator->get($this->authenName);
        
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        Yii::$app->authenticator->remove($this->authenName);
        return $valid;
    }
}