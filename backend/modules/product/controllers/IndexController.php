<?php
namespace backend\modules\product\controllers;

use common\models\product\Product;
use yii;
use common\models\product\ProductType;
use common\helpers\FormHelper;

class IndexController extends \backend\controllers\MController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = ProductType::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new ProductType();
            }
            $data['html'] = $this->renderPartial('add', [
                'model' => $model
            ]);
        }
        
        return $this->asJson($data);
    }

    public function actionList()
    {
        $model = ProductType::find();
        $model->andFilterWhere([
            'name' => $this->request->post('name')
        ]);
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        
        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'name' => $m->name,
                'activation_money' => $m->activation_money,
                'add_user' => $m->add_user,
                'created_at' => date('Y-m-d H:i:s', $m->created_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        if ($this->request->isPost) {
            $model = ProductType::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new ProductType();
            }
            $model->load([
                'name' => $this->request->post('name'),
                /*'level_cc_settlement' => $this->request->post('level_cc_settlement'),
                'level_dc_settlement' => $this->request->post('level_dc_settlement'),
                'capping' => $this->request->post('capping'),*/
                'activation_money' => $this->request->post('activation_money'),
                'add_user' => Yii::$app->user->identity->username
            ], '');
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
        }
        return $this->redirect([
            'index'
        ]);
    }
}