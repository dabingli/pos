<?php
namespace backend\modules\app\controllers;

use yii;
use common\helpers\FormHelper;
use backend\modules\app\controllers\BaseController;
use common\models\app\AppSettings;

class SettingsController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $name = $this->request->post('name');
        $model = AppSettings::find();
        $model->andFilterWhere([
            'name' => $name
        ]);
        $data = [];
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $app_settings = $model->all();
        foreach ($app_settings as $key => $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'name' => $val->name,
                'mobile' => $val->mobile,
                'create_name' => $val->create_name,
                'update_name' => $val->update_name,
                'created_at' => date('Y-m-d H:i:s', $val->created_at),
                'updated_at' => date('Y-m-d H:i:s', $val->updated_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('add', [
                'is_newRecord' => false
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        $user_name = Yii::$app->user->identity->user_name;
        $mobile = $this->request->post('mobile');
        $name = $this->request->post('name');
        
        $model = new AppSettings();
        // $model->load([
        // 'mobile'=> $mobile,
        // 'name' => $name,
        // 'create_name' => $user_name
        // ],'');
        $model->mobile = $mobile;
        $model->name = $name;
        $model->create_name = $user_name;
        $isNewRecord = $model->isNewRecord;
        if ($model->save()) {
            if ($isNewRecord) {
                Yii::$app->session->setFlash('success', '添加成功');
            } else {
                Yii::$app->session->setFlash('success', '修改成功');
            }
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

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $id = $this->request->post('id');
            $app_settings = AppSettings::findOne([
                'id' => $id
            ]);
            $data['html'] = $this->renderPartial('edit', [
                'model' => $app_settings,
                'is_newRecord' => true
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        $user_name = Yii::$app->user->identity->user_name;
        $mobile = $this->request->post('mobile');
        $name = $this->request->post('name');
        $id = $this->request->post('id');
        
        $model = AppSettings::findOne([
            'id' => $id
        ]);
        $model->load([
            'mobile' => $mobile,
            'name' => $name,
            'create_name' => $user_name
        ], '');
        // $model->save();
        // print_r($model->getFirstErrors());die;
        // $model->mobile = $mobile;
        // $model->name = $name;
        // $model->update_name = $user_name;
        // $isNewRecord = $model->isNewRecord;
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