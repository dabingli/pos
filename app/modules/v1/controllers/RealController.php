<?php
namespace app\modules\v1\controllers;

use yii;
use yii\filters\auth\HttpBearerAuth;

class RealController extends BaseActiveController
{

    public $modelClass = 'common\models\users\UserIdentityAudit';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'optional' => []
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [

            'bank-card-query' => [
                'class' => 'app\modules\v1\actions\real\BankCardQueryAction',
                'accountNo' => $this->request->post('accountNo'),
                'idCardCode' => $this->request->post('idCardCode'),
                'name' => $this->request->post('name'),
                'identity_front_images' => $this->request->post('identity_front_images'),
                'identity_back_images' => $this->request->post('identity_back_images'),
                'identity_personal_images' => $this->request->post('identity_personal_images'),
                'hold_identity_images' => $this->request->post('hold_identity_images'),
                'bank' => $this->request->post('bank'),
                'code' => $this->request->post('code'),
                'mobile' => $this->request->post('mobile')
            ],

            'authentication-code' => [
                'class' => 'app\components\captcha\code\SMSCodeAction',
                'length' => 6,
                'fixedVerifyCode' => YII_ENV_DEV ? '1234' : null,
                'codeId' => 6,
                'content' => Yii::$app->params['sms']['sign'] . '您的实名验证码为',
                'mobile' => $this->request->post('mobile')
            ],
        ];
    }
}