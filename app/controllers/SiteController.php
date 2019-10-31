<?php
namespace app\controllers;

use common\library\changjie\ReceiveOrder;
use common\models\user\User;
use yii;
use common\services\app\ShareRegisterForm;
use common\library\changjie\PrePay;
use common\models\agent\Agent;
use common\models\CashOrder;
use common\models\Order;

class SiteController extends \yii\web\Controller
{

    /**
     * 错误页面
     *
     * @return \yii\web\Response|string
     */

    public function actions()
    {
        return [
            'register-code' => [
                'class' => 'app\components\captcha\code\SMSCodeAction',
                'length' => 4,
                'fixedVerifyCode' => YII_ENV_DEV ? '1234' : null,
                'codeId' => 1,
                'content' => Yii::$app->params['sms']['sign'] . '您的注册验证码为',
                'mobile' => Yii::$app->request->post('mobile'),
                'parent_mobile' => Yii::$app->request->post('parent_user_mobile')
            ]
        ];
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        $code = $exception->statusCode;
        return $this->asJson([
            'status' => $code,
            'code' => 0,
            'message' => [
                '页面发生错误，请联系开发人员'
            ]
        ]);
    }

    public function actionIndex()
    {
        return 200;
    }

    /**
     * 系统相关的配置说明
     *
     * @return mixed[]|array[]
     */
    public function actionSystem()
    {
        $data['service'] = [
            'version' => Yii::$app->version,
            'web_logo' => Yii::$app->debris->config('web_logo'),
            'cdnUrl' => Yii::$app->debris->config('system_cdn'),
            'web_site_title' => Yii::$app->debris->config('web_site_title'),
            'web_site_icp' => Yii::$app->debris->config('web_site_icp'),
            'web_seo_keywords' => Yii::$app->debris->config('web_seo_keywords'),
            'web_seo_description' => Yii::$app->debris->config('web_seo_description'),
            'web_copyright' => Yii::$app->debris->config('web_copyright')
        ];
        $data['app'] = [
            'app_name' => Yii::$app->debris->config('app_name'),
            'customer_telephone' => Yii::$app->debris->config('customer_telephone'),
            'app_version' => Yii::$app->debris->config('app_version'),
            'app_image' => Yii::$app->debris->config('app_image')
        ];
        return $this->asJson([
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ]);
    }

    /**
     * 分享注册
     */
    public function actionRegister()
    {
        $this->layout = false;
        $mobile = Yii::$app->request->get('mobile');
        $user_id = Yii::$app->request->get('user_id');
        return $this->renderPartial('share/register', [
            'mobile' => $mobile,
            'user_id' => $user_id
        ]);
    }

    /**
     * 注册
     */
    public function actionRegisterDo()
    {
        $post = Yii::$app->request->post();
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $model = new ShareRegisterForm();
        if ($model->load($post, '')) {
            $user = $model->signup();
            if ($user) {
                $transaction->commit();
                return $this->asJson([
                    'status' => 0,
                    'code' => 200,
                    'message' => '注册成功',
                    'data' => $user
                ]);
            }else{
                return $this->asJson([
                    'status' => 0,
                    'code' => 0,
                    'message' => $model->getFirstErrors(),
                    'data' => ''
                ]);
            }
        }
        $transaction->rollBack();
        return $this->asJson([
            'status' => 0,
            'code' => 0,
            'message' => $model->getFirstErrors(),
            'data' => []
        ]);
    }
}