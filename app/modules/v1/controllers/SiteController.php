<?php
namespace app\modules\v1\controllers;

use yii;
use yii\web\Controller;
use yii\filters\auth\HttpBearerAuth;

class SiteController extends Controller
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'app\components\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_DEV ? '1234' : null,
                'height' => 35,
                'width' => 80,
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 2
            ]
        ]; // 如果用户觉得不好认的话可以加大这个值
    }

    /**
     * APP每次调前如果没有token缓存就得调该接口获得token
     *
     * @return \yii\web\Response
     */
    public function actionGetAccessToken()
    {
        $token = Yii::$app->authenticator->getAccessToken();
        return $this->asJson([
            'code' => 200,
            'status' => 0,
            'message' => [
                '获取成功'
            ],
            'data' => [
                'token' => $token,
                'authTimeout' => Yii::$app->authenticator->authTimeout
            ]
        ]);
    }
}