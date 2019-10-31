<?php
namespace common\controllers;

use Yii;
use yii\web\Controller;
use common\helpers\FormHelper;

class BaseController extends Controller
{

    /**
     * 默认分页
     *
     * @var int
     */
    protected $pageSize = 10;

    /**
     * 解析错误
     *
     * @param
     *            $fistErrors
     * @return string
     */
    protected function analyErr($firstErrors)
    {
        return addslashes(Yii::$app->debris->analyErr($firstErrors));
    }

    protected $request;

    protected $response;

    public function init()
    {
        parent::init();
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
    }

    public function multiErrors2Msg($errors)
    {
        return addslashes(FormHelper::multiErrors2Msg($errors));
    }

    public function beforeAction($action)
    {
        // $this->xhprofStart();
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        // $this->xhprofEnd();
        return parent::afterAction($action, $result);
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
}