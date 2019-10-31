<?php
namespace backend\modules\app\controllers;

use yii;
use common\helpers\FormHelper;
use backend\modules\app\controllers\BaseController;
use common\models\app\AppShare;

class ShareController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        // var_dump();die;
        $model = AppShare::find();
        $model->with('app');
        $data = [];
        $data['total'] = $model->count();
        $model->orderBy([
            'sort' => SORT_DESC
        ]);
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $share = $model->all();
        foreach ($share as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'name' => $val->app->name,
                'sort' => $val->sort,
                'description' => $val->description,
                'image' => $val->image,
                'created_at' => date('Y-m-d H:i:s', $val->created_at),
                'add_name' => $val->add_name,
                'status' => $val->getStatus(),
                'o_status' => $val->status
            ];
        }
        return $this->asJson($data);
    }

    function actionStart()
    {
        if ($this->request->isAjax) {
            $model = AppShare::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => AppShare::START
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '启用成功');
        }
        
        return $this->asJson([]);
    }

    function actionStop()
    {
        if ($this->request->isAjax) {
            $model = AppShare::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => AppShare::STOP
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '停用成功');
        }
        
        return $this->asJson([]);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $status = AppShare::StatusLabels();
            $data['html'] = $this->renderPartial('add', [
                'status' => $status
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        $post = $this->request->post();
        $user_name = Yii::$app->user->identity->username;
        $model = new AppShare();
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
            $status = AppShare::StatusLabels();
            $app_share = AppShare::findOne([
                'id' => $id
            ]);
            // var_dump($app_share);die;
            $data['html'] = $this->renderPartial('edit', [
                'status' => $status,
                'app_share' => $app_share
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        $post = $this->request->post();
        $user_name = Yii::$app->user->identity->username;
        $model = AppShare::findOne([
            'id' => $post['id']
        ]);
        
        $post['add_name'] = $user_name;
        $post['app_id'] = 1;
        // var_dump($post);die;
        $model->load($post, '');
        // var_dump($model->validate());die;
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
        $model = AppShare::findOne([
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