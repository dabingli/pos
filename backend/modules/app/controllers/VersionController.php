<?php
namespace backend\modules\app\controllers;

use yii;
use common\helpers\FormHelper;
use backend\modules\app\controllers\BaseController;
use common\models\app\AppVersion;

class VersionController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = AppVersion::find();
        $model->with('app');
        $data = [];
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $version = $model->all();
        foreach ($version as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'name' => $val->app->name,
                'version' => $val->version,
                'description' => $val->description,
                'url' => $val->url,
                'created_at' => date('Y-m-d H:i:s', $val->created_at),
                'create_name' => $val->create_name,
                'type' => $val->getType(),
                'status' => $val->getStatus(),
                'is_allow_update' => $val->getAllowUpdate()
            ];
        }
        return $this->asJson($data);
    }

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $id = $this->request->post('id');
            $app_version = AppVersion::findOne([
                'id' => $id
            ]);
            $d['status_text'] = AppVersion::StatusLabels();
            $d['type_text'] = AppVersion::TypeLabels();
            $d['is_allow_update_text'] = AppVersion::AllowUpdateLabels();
            $data['html'] = $this->renderPartial('edit', [
                'model' => $app_version,
                'data' => $d
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        // var_dump($this->request->post());die;
        // var_dump(Yii::$app->user->identity);die;
        $user_name = Yii::$app->user->identity->username;
        $type = $this->request->post('type');
        $version = $this->request->post('version');
        $description = $this->request->post('description');
        $url = $this->request->post('url');
        $status = $this->request->post('status');
        $is_allow_update = $this->request->post('is_allow_update');
        
        $id = $this->request->post('id');
        
        $model = AppVersion::findOne([
            'id' => $id
        ]);
        $model->load([
            'type' => $type,
            'version' => $version,
            'create_name' => $user_name,
            'description' => $description,
            'url' => $url,
            'status' => $status,
            'is_allow_update' => $is_allow_update
        ], '');
        
        // var_dump($user_name);die;
        // $model->save();
        // print_r($model->getFirstErrors());die;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '修改成功');
        } else {
            $msg = FormHelper::multiErrors2Msg($model->errors);
            if (! empty($msg)) {
                Yii::$app->session->setFlash('danger', $msg);
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }
}