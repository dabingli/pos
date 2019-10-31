<?php
namespace app\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\rest\ActiveController;

abstract class BaseController extends ActiveController
{

    public $request;

    public $response;

    const LIMIT = 20;

    public $allowAgentActions = [];

    public function init()
    {
        parent::init();
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        // $this->on(self::EVENT_BEFORE_ACTION, [
        // $this,
        // 'xhprofStart'
        // ]);
        // $this->on(self::EVENT_AFTER_ACTION, [
        // $this,
        // 'xhprofStart'
        // ]);
    }

    public function xhprofStart()
    {
        if (function_exists('xhprof_enable')) {
            xhprof_enable();
        }
    }

    public function xhprofEnd()
    {
        if (function_exists('xhprof_disable')) {
            $xhprofData = xhprof_disable();
            $xhprofRoot = Yii::getAlias('@common/library/xhprof_lib/');
            include_once $xhprofRoot . "utils/xhprof_lib.php";
            include_once $xhprofRoot . "utils/xhprof_runs.php";
            $xhprofRuns = new \XHProfRuns_Default();
            $route = '{' . $this->module->id . '}' . '{' . $this->id . '}' . '{' . $this->action->id . '}';
            $xhprofRuns->save_run($xhprofData, $route);
        }
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ]
        ];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (false === Yii::$app->authenticator->isAuth()) {
            echo json_encode([
                'code' => 503,
                'status' => 1,
                'message' => [
                    '授权失败'
                ],
                'data' => [
                    'token' => Yii::$app->authenticator->getAccessToken()
                ]
            ]);
            return false;
        }
        
        return parent::beforeAction($action);
    }

    public function asJson($data)
    {
        return parent::asJson($data);
    }
}