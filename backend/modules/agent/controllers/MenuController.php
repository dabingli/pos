<?php
namespace backend\modules\agent\controllers;

use Yii;
use common\models\agent\AgentMenu as Menu;
use common\models\agent\AgentMenuCate as MenuCate;
use common\helpers\ArrayHelper;
use common\components\CurdTrait;
use backend\modules\agent\controllers\BaseController;

class MenuController extends BaseController
{
    use CurdTrait;

    /**
     *
     * @var
     *
     */
    public $modelClass = 'common\models\agent\AgentMenu';

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $cate_id = Yii::$app->request->get('cate_id', MenuCate::getFirstDataID());
        $models = Menu::find()->filterWhere([
            'cate_id' => $cate_id
        ])
            ->orderBy('sort asc, created_at asc')
            ->asArray()
            ->all();
        
        return $this->render('index', [
            'models' => ArrayHelper::itemsMerge($models),
            'cate_id' => $cate_id,
            'cates' => MenuCate::getList()
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return array|mixed|string|\yii\web\Response
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        
        $model = $this->findModel($id);
        $model->level = $request->get('level', null) ?? $model->level; // 级别
        $model->pid = $request->get('pid', null) ?? $model->pid; // 父id
        $model->cate_id = $request->get('cate_id', 0) ?? $model->cate_id; // 分类id
        $model->params = unserialize($model->params);
        
        if ($model->load($request->post())) {
            if ($request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
            
            $model->params = serialize($model->params);
            return $model->save() ? $this->message('操作成功', $this->redirect([
                'index',
                'cate_id' => $model->cate_id
            ])) : $this->message($this->analyErr($model->getFirstErrors()), $this->redirect([
                'index',
                'cate_id' => $model->cate_id
            ]), 'error');
        }
        
        return $this->renderAjax('ajax-edit', [
            'model' => $model,
            'parent_title' => $request->get('parent_title', '无')
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
        $cate_id = Yii::$app->request->get('cate_id');
        if ($this->findModel($id)->delete()) {
            return $this->message("删除成功", $this->redirect([
                'index',
                'cate_id' => $cate_id
            ]));
        }
        
        return $this->message("删除失败", $this->redirect([
            'index',
            'cate_id' => $cate_id
        ]), 'error');
    }
}