<?php
namespace frontend\modules\sys\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\components\CurdTrait;
use common\models\agent\AgentUser as Manager;
use frontend\modules\sys\models\PasswdForm;
use frontend\modules\sys\models\ManagerForm;
use common\helpers\ArrayHelper;

class ManagerController extends \frontend\controllers\MController
{
    use CurdTrait;

    /**
     *
     * @var string
     */
    public $modelClass = 'common\models\agent\AgentUser';

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword', null);
        
        // 获取当前用户权限的下面的所有用户id，除超级管理员
        $authIds = Yii::$app->services->agent->auth->getChildRoleIds();
        
        $data = Manager::find()->filterWhere([
            'in',
            'id',
            $authIds
        ])
            ->andFilterWhere([
            'or',
            [
                'like',
                'username',
                $keyword
            ],
            [
                'like',
                'mobile',
                $keyword
            ],
            [
                'like',
                'account',
                $keyword
            ]
        ])
            ->andWhere([
            'agent_id' => $this->agentId
        ]);
        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $this->pageSize
        ]);
        $models = $data->offset($pages->offset)
            ->orderBy('root asc, id desc')
            ->limit($pages->limit)
            ->all();
        
        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages,
            'keyword' => $keyword
        ]);
    }

    /**
     * 个人中心
     *
     * @return mixed|string
     */
    public function actionPersonal()
    {
        $id = Yii::$app->user->id;
        $model = $this->findModel($id);
        
        // 提交表单
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('修改个人信息成功', $this->redirect([
                'personal'
            ]));
        }
        
        return $this->render($this->action->id, [
            'model' => $model
        ]);
    }

    /**
     * 用户信息
     *
     * @return mixed|string
     */
    public function actionView()
    {
        $model = $this->findModel($this->request->get('id'));
        $data['html'] = $this->render('view', [
            'model' => $model
        ]);

        return $this->asJson($data);
    }

    /**
     * 修改密码
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionUpPassword()
    {
        $model = new PasswdForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /* @var $manager \common\models\sys\Manager */
            $manager = Yii::$app->user->identity;
            $manager->password_hash = Yii::$app->security->generatePasswordHash($model->passwd_new);
            ;
            
            // 记录行为日志
            Yii::$app->services->agent->log('updateManagerPwd', '修改管理员密码|账号:' . $manager->user_name, false);
            
            if ($manager->save()) {
                // 退出登陆
                Yii::$app->user->logout();
                return $this->goHome();
            }
        }
        
        return $this->render($this->action->id, [
            'model' => $model
        ]);
    }

    /**
     * ajax编辑/创建
     *
     * @return array|mixed|string|Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        
        $model = new ManagerForm();
        $model->id = $request->get('id');
        $model->agent_id = $this->agentId;
        $model->add_user_name = Yii::$app->user->identity->user_name;
        $model->loadData();
        
        $model->root != Manager::ROOT && $model->scenario = 'generalAdmin';
        if ($model->load($request->post())) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            
            // 记录行为日志
            Yii::$app->services->agent->log('managerEdit', '创建/编辑管理员密码|账号:' . $model->account, false);
            return $model->saveData() ? $this->message('操作成功', $this->redirect([
                'index'
            ])) : $this->message($this->analyErr($model->getFirstErrors()), $this->redirect([
                'index'
            ]), 'error');
        }
        
        list ($roles, $parent_key, $treeStat) = Yii::$app->services->agent->auth->getChildRoles();
        $roles = ArrayHelper::itemsMerge($roles, $parent_key, 'key', 'parent_key');
        $roles = ArrayHelper::itemsMergeDropDown($roles, 'key', 'name', $treeStat);
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'roles' => $roles
        ]);
    }

    /**
     * 删除
     *
     * @param
     *            $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        // 记录行为日志
        Yii::$app->services->agent->log('managerDel', '删除管理员');
        
        if ($this->findModel([
            'id' => $id,
            'agent_id' => $this->agentId
        ])->delete()) {
            return $this->message("删除成功", $this->redirect([
                'index'
            ]));
        }
        
        return $this->message("删除失败", $this->redirect([
            'index'
        ]), 'error');
    }
}