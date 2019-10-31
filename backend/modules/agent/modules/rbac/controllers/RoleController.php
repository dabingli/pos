<?php
namespace backend\modules\agent\modules\rbac\controllers;

use yii;
use common\models\entities\StoreMenu;
use common\models\entities\Agent;
use common\helpers\FormHelper;
use common\models\entities\AgentUser;
use common\models\entities\MechanismRole;
use common\models\entities\MechanismRoleEnterprise;

class RoleController extends \backend\modules\agent\modules\rbac\controllers\BaseController
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
            $model = MechanismRole::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new MechanismRole();
            }
            $data['html'] = $this->renderPartial('add', [
                'agent' => $agent,
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        if ($this->request->isPost) {
            $load = [
                'name' => $this->request->post('name'),
                'description' => $this->request->post('description'),
                'update_user' => Yii::$app->user->identity->user_name
            ];
            $model = MechanismRole::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new MechanismRole();
                $load['add_user'] = Yii::$app->user->identity->user_name;
            }
            $model->load($load, '');
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if ($model->save()) {
                MechanismRoleEnterprise::deleteAll([
                    'mechanism_role_id' => $model->id
                ]);
                
                foreach ($this->request->post('agent_id') as $agentId) {
                    $data[] = [
                        $agentId,
                        $model->id
                    ];
                }
                
                if (! empty($data)) {
                    Yii::$app->db->createCommand()
                        ->batchInsert(MechanismRoleEnterprise::tableName(), [
                        'agent_id',
                        'mechanism_role_id'
                    ], $data)
                        ->execute();
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '添加成功');
            } else {
                $transaction->rollBack();
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('danger', $msg);
                } else {
                    Yii::$app->session->setFlash('danger', '操作失败');
                }
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionList()
    {
        $model = MechanismRole::find();
        $model->andFilterWhere([
            'like',
            'name',
            $this->request->post('name')
        ]);
        $model->with('mechanismRoleEnterprise.agent');
        $data['total'] = $model->count();
        $limit = intval($this->request->post('limit'));
        $offset = intval($this->request->post('offset'));
        $model->limit($limit)->offset($offset);
        $data['rows'] = [];
        $model->orderBy([
            'updated_at' => SORT_DESC,
            'id' => SORT_DESC
        ]);
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'agent_name' => implode('<br />', array_column(array_column($m->mechanismRoleEnterprise, 'agent'), 'name')),
                'name' => $m->name,
                'description' => $m->description,
                'update_user' => $m->update_user,
                'add_user' => $m->add_user,
                'updated_at' => date('Y-m-d H:i:s', $m->updated_at),
                'created_at' => date('Y-m-d H:i:s', $m->created_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionDelete()
    {
        $model = MechanismRole::find()->andWhere([
            'id' => $this->request->post('id')
        ]);
        foreach ($model->all() as $m) {
            $m->delete();
        }
        Yii::$app->session->setFlash('success', '删除成功');
        return $this->asJson([]);
    }

    public function actionItem()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('item');
        }
        
        return $this->asJson($data);
    }

    public function actionAddPerms()
    {
        if ($this->request->isPost) {
            $model = MechanismRole::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                Yii::$app->session->setFlash('danger', '操作失败');
                return $this->redirect([
                    'index'
                ]);
            }
            $perms = (array) $this->request->post('perms');
            $perms = array_unique($perms);
            $model->perms = json_encode($perms);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
            return $this->redirect([
                'index'
            ]);
        }
    }
}