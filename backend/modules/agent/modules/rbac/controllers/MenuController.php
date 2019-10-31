<?php
namespace backend\modules\agent\modules\rbac\controllers;

use yii;
use common\models\entities\AgentMenu;
use common\helpers\FormHelper;

class MenuController extends \backend\modules\agent\modules\rbac\controllers\BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEdit($id = '')
    {
        $storeMenu = AgentMenu::findOne([
            'id' => $id
        ]);
        if (empty($storeMenu)) {
            $storeMenu = new AgentMenu();
        }
        if ($this->request->isPost) {
            $storeMenu->load([
                'name' => $this->request->post('name'),
                'route' => $this->request->post('route'),
                'parent_id' => $this->request->post('parent_id'),
                'order' => $this->request->post('order'),
                'remarks' => $this->request->post('remarks'),
                'icon' => $this->request->post('icon')
            ], '');
            $isNewRecord = $storeMenu->isNewRecord;
            if ($storeMenu->save()) {
                if ($isNewRecord) {
                    Yii::$app->session->setFlash('success', '添加成功');
                } else {
                    Yii::$app->session->setFlash('success', '修改成功');
                }
            } else {
                $msg = FormHelper::multiErrors2Msg($storeMenu->errors);
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
        return $this->render('edit', [
            'model' => $storeMenu
        ]);
    }

    public function actionDelete()
    {
        if ($this->request->isAjax) {
            $model = AgentMenu::findOne([
                'id' => $this->request->post('id')
            ]);
            
            if ($model && $model->delete()) {
                Yii::$app->session->setFlash('success', '删除成功');
            } else {
                Yii::$app->session->setFlash('danger', '删除失败');
            }
        }
        return $this->asJson([]);
    }
}