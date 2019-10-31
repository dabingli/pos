<?php
namespace backend\modules\app\controllers;

use yii;
use yii\web\UploadedFile;
use common\helpers\FormHelper;
use common\models\app\AppAdvertise;
use backend\modules\app\controllers\BaseController;

class AdvertiseController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = AppAdvertise::find();
        $model->with('app');
        $data = [];
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->orderBy([
            'sort' => SORT_DESC
        ]);
        $model->offset($this->request->post('offset'));
        $advertise = $model->all();
//        $sysConfig = Yii::$app->debris->configAll();
        foreach ($advertise as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'name' => $val->app->name,
                'sort' => $val->sort,
                'description' => $val->description,
                'url' => $val->url,
                'image' => $val->image,
                'created_at' => date('Y-m-d H:i:s', $val->created_at),
                'add_name' => $val->add_name,
                'type' => $val->getType(),
                'status' => $val->getStatus(),
                'o_status' => $val->status
            ];
        }
        return $this->asJson($data);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $status = AppAdvertise::StatusLabels();
            $type = AppAdvertise::TypeLabels();
            
            $data['html'] = $this->renderPartial('add', [
                'status' => $status,
                'type' => $type
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        // var_dump($_FILES);die;
        $post = $this->request->post();
        $user_name = Yii::$app->user->identity->username;
        $model = new AppAdvertise();
        $post['add_name'] = $user_name;
        $post['app_id'] = 1;
        // var_dump($post);die;
        $model->load($post, '');
        // var_dump($model->validate());die;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '添加成功');
        } else {
            $msg = FormHelper::multiErrors2Msg($model->errors);
            if (! empty($msg)) {
                Yii::$app->session->setFlash('error', $msg);
            } else {
                Yii::$app->session->setFlash('error', '操作失败');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $id = $this->request->post('id');
            $app_advertise = AppAdvertise::findOne([
                'id' => $id
            ]);
            $status = AppAdvertise::StatusLabels();
            $type = AppAdvertise::TypeLabels();
            
            $data['html'] = $this->renderPartial('edit', [
                'status' => $status,
                'type' => $type,
                'app_advertise' => $app_advertise
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        // var_dump($_FILES);die;
        $post = $this->request->post();
        $model = AppAdvertise::findOne([
            'id' => $post['id']
        ]);
        $model->load($post, '');
        // var_dump($post);die;
        // var_dump($model->validate());
        // print_r($model->toArray());die;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '修改成功');
        } else {
            $msg = FormHelper::multiErrors2Msg($model->errors);
            if (! empty($msg)) {
                Yii::$app->session->setFlash('error', $msg);
            } else {
                Yii::$app->session->setFlash('error', '操作失败');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    //    修改状态
    function actionAjaxEdit()
    {
        $model = AppAdvertise::findOne([
            'id' => $this->request->post('pk')
        ]);
        $name = $this->request->post('name');
        $value = $this->request->post('value');
        $model->$name = $value;
        if($model->save()){
            return $this->asJson([
                'status' => 1,
                'msg' => '修改成功',
                'data' => ''
            ]);
        }
        return $this->asJson([
            'status' => -1,
            'msg' => '修改失败',
            'data' => ''
        ]);
    }
}