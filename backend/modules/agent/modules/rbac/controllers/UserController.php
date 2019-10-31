<?php
namespace backend\modules\agent\modules\rbac\controllers;

use yii;
use common\models\agent\Agent;
use common\services\AgentSignupForm;
use common\helpers\FormHelper;
use common\models\agent\AgentUser;
use common\services\StoreMenuServices;

class UserController extends \backend\modules\agent\modules\rbac\controllers\BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAdd()
    {
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $agentModel = Agent::find();
            $agentModel->andWhere([
                'status' => Agent::START
            ]);
            $agentModel->select([
                'name'
            ]);
            $agent = $agentModel->indexBy('id')->column();
            $model = AgentUser::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new AgentUser();
            }
            $data['html'] = $this->renderPartial('add', [
                'agent' => $agent,
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEdit()
    {
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $agentModel = Agent::find();
            $agentModel->andWhere([
                'status' => Agent::START
            ]);
            $agentModel->select([
                'name'
            ]);
            $agent = $agentModel->indexBy('id')->column();
            $model = AgentUser::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new AgentUser();
            }
            $data['html'] = $this->renderPartial('edit', [
                'agent' => $agent,
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        if ($this->request->isPost) {
            $model = AgentUser::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                Yii::$app->session->setFlash('danger', '用户不存在');
            } else {
                $model->load([
                    'agent_id' => $this->request->post('agent_id'),
                    'user_name' => $this->request->post('user_name'),
                    'admin_name' => $this->request->post('admin_name'),
                    'account' => $this->request->post('account'),
                    'number' => $this->request->post('number'),
                    'mobile' => $this->request->post('mobile'),
                    'mailbox' => $this->request->post('mailbox'),
                    'remarks' => $this->request->post('remarks')
                ], '');
                if (! $model->save()) {
                    $msg = FormHelper::multiErrors2Msg($model->errors);
                    if (! empty($msg)) {
                        Yii::$app->session->setFlash('error', $msg);
                    } else {
                        Yii::$app->session->setFlash('error', '修改失败');
                    }
                } else {
                    Yii::$app->session->setFlash('success', '修改成功');
                }
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionAddDo()
    {
        if ($this->request->isPost) {
            $model = new AgentSignupForm();
            $post = $this->request->post();
            $post['add_user_name'] = Yii::$app->user->identity->username;
            $post['root'] = AgentUser::ROOT;
            $model->load($post, '');
            if ($model->signup()) {
                Yii::$app->session->setFlash('success', '添加成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('danger', $msg);
                } else {
                    Yii::$app->session->setFlash('danger', '添加失败');
                }
            }
        }
        
        return $this->redirect([
            'index'
        ]);
    }

    public function actionAccount($account, $id = '')
    {
        $model = AgentUser::findOne([
            'account' => $account
        ]);
        if ($model && empty($id)) {
            return $this->asJson(false);
        } elseif ($model && $model->id != $id) {
            return $this->asJson(false);
        }
        return $this->asJson(true);
    }

    public function actionList()
    {
        $model = AgentUser::find();
        $model->andFilterWhere([
            'status' => $this->request->post('status')
        ]);
        $model->andWhere([
            'root' => AgentUser::ROOT
        ]);
        $model->andFilterWhere([
            'like',
            'account',
            $this->request->post('account')
        ]);
        $model->andFilterWhere([
            'like',
            'user_name',
            $this->request->post('user_name')
        ]);
        $model->with('agent');
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        
        $data['rows'] = [];
        
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'agent' => isset($m->agent->name) ? $m->agent->name : '',
                'agent_number' => isset($m->agent->number) ? $m->agent->number : '',
                'agent_id' => $m->agent_id,
                'account' => $m->account,
                'user_name' => $m->user_name,
                'number' => $m->number,
                'mobile' => $m->mobile,
                'mailbox' => $m->mailbox,
                'remarks' => $m->remarks,
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'status' => $m->getStatus()
            ];
        }
        return $this->asJson($data);
    }

    function actionStop()
    {
        if ($this->request->isAjax) {
            $model = AgentUser::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => AgentUser::STOP
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '停用成功');
        }
        
        return $this->asJson([]);
    }

    function actionStart()
    {
        if ($this->request->isAjax) {
            $model = AgentUser::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => AgentUser::START
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '启用成功');
        }
        
        return $this->asJson([]);
    }

    public function actionView()
    {
        $model = AgentUser::findOne([
            'id' => $this->request->post('id')
        ]);
        $data['html'] = $this->renderPartial('view', [
            'model' => $model
        ]);
        
        return $this->asJson($data);
    }

    public function actionDelete()
    {
        if ($this->request->isAjax) {
            $model = AgentUser::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->delete();
            }
            Yii::$app->session->setFlash('success', '删除成功');
        }
        
        return $this->asJson([]);
    }

    public function actionPassword()
    {
        $model = AgentUser::findOne([
            'id' => $this->request->post('id')
        ]);
        $data['html'] = $this->renderPartial('password', [
            'model' => $model
        ]);
        
        return $this->asJson($data);
    }

    public function actionPasswordDo()
    {
        $model = AgentUser::findOne([
            'id' => $this->request->post('id')
        ]);
        $model->setPassword($this->request->post('password'));
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '密码重置成功');
        } else {
            $msg = FormHelper::multiErrors2Msg($model->errors);
            if (! empty($msg)) {
                Yii::$app->session->setFlash('danger', $msg);
            } else {
                Yii::$app->session->setFlash('danger', '密码重置失败');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }
}