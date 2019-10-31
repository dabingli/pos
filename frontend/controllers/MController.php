<?php
namespace frontend\controllers;

use Yii;
use yii\web\UnauthorizedHttpException;
use yii\filters\AccessControl;
use frontend\helpers\AuthHelper;
use common\models\user\User;

class MController extends \common\controllers\BaseController
{

    protected $agentId;

    protected $agentModel;

    protected $agentAppUser;

    /**
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ] // 登录
                    ]
                ]
            ]
        ];
    }

    /**
     * RBAC验证
     *
     * @param
     *            $action
     * @return bool
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (! parent::beforeAction($action)) {
            return false;
        }

        $this->agentId = isset(Yii::$app->user->identity->agent_id) ? intval(Yii::$app->user->identity->agent_id) : null;
        $this->view->params['user'] = Yii::$app->user->identity;
        Yii::$app->params['userModel'] = $this->view->params['user'];
        $this->agentModel = $this->view->params['user']->agent;
        Yii::$app->params['agentModel'] = $this->agentModel;
        Yii::$app->params['adminTitle'] = $this->agentModel->admin_name . '●开店宝';
        if (empty($this->agentId)) {
            throw new UnauthorizedHttpException('请求页面不存在', 404);
        }
        $this->getAgentAppUser();
        // 分页
        Yii::$app->debris->config('sys_page') && $this->pageSize = Yii::$app->debris->config('sys_page');
        
        // 验证是否登录且验证是否超级管理员
        if (! Yii::$app->user->isGuest && Yii::$app->services->agent->isAuperAdmin()) {
            return true;
        }
        
        // 控制器+方法
        $permissionName = '/' . Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        // 加入模块验证
        if (Yii::$app->controller->module->id != "app-frontend") {
            $permissionName = '/' . Yii::$app->controller->module->id . $permissionName;
        }
        // 判断是否忽略校验
        if (in_array($permissionName, Yii::$app->params['noAuthRoute'])) {
            return true;
        }
        // 开始权限校验
        if (! AuthHelper::verify($permissionName)) {
            throw new UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
        }
        
        return true;
    }

    public function getAgentAppUser()
    {
        $this->agentAppUser = User::findOne([
            'mobile' => $this->agentModel->mobile,
            'agent_id' => $this->agentId
        ]);
        Yii::$app->params['agentAppUser'] = $this->agentAppUser;
    }

    /**
     * 错误提示信息
     *
     * @param string $msgText
     *            错误内容
     * @param string $skipUrl
     *            跳转链接
     * @param string $msgType
     *            提示类型 [success/error/info/warning]
     * @return mixed
     */
    public function message($msgText, $skipUrl, $msgType = null)
    {
        $msgType = $msgType ?? 'success';
        ! in_array($msgType, [
            'success',
            'error',
            'info',
            'warning'
        ]) && $msgType = 'success';
        
        Yii::$app->getSession()->setFlash($msgType, $msgText);
        
        return $skipUrl;
    }
}