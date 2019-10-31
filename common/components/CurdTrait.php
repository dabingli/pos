<?php
namespace common\components;

use Yii;
use yii\data\Pagination;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use common\helpers\ResultDataHelper;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

trait CurdTrait
{

    /**
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException('"modelClass" 属性必须设置.');
        }
        
        parent::init();
    }

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = $this->modelClass::find()->where([
            '>=',
            'status',
            StatusEnum::DISABLED
        ]);
        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $this->pageSize
        ]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();
        
        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        if ($model->load($request->post()) && $model->save()) {
            return $this->message("修改成功", $this->redirect([
                'index'
            ]));
        }

        return $this->render($this->action->id, [
            'model' => $model
        ]);
    }

    /**
     * 伪删除
     *
     * @param
     *            $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        if (! ($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect([
                'index'
            ]), 'error');
        }
        
        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect([
                'index'
            ]));
        }
        
        return $this->message("删除失败", $this->redirect([
            'index'
        ]), 'error');
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
        if ($this->findModel($id)->delete()) {
            return $this->message("删除成功", $this->redirect([
                'index'
            ]));
        }
        
        return $this->message("删除失败", $this->redirect([
            'index'
        ]), 'error');
    }

    /**
     * ajax更新排序/状态
     *
     * @param
     *            $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (! ($model = $this->modelClass::findOne($id))) {
            return $this->message('找不到数据', $this->redirect([
                'index'
            ]), 'error');
        }
        
        $data = ArrayHelper::filter(Yii::$app->request->get(), [
            'sort',
            'status'
        ]);
        $model->attributes = $data;
        if (! $model->save()) {
            return $this->message($this->analyErr($model->getFirstErrors()), $this->redirect([
                'index'
            ]), 'error');
        }
        return $this->message("修改成功", $this->redirect([
            'index'
        ]));
    }

    /**
     * ajax编辑/创建
     *
     * @return array
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);
        if ($model->load($request->post())) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            
            return $model->save() ? $this->message('操作成功', $this->redirect([
                'index'
            ])) : $this->message($this->analyErr($model->getFirstErrors()), $this->redirect([
                'index'
            ]), 'error');
        }
        
        return $this->renderAjax($this->action->id, [
            'model' => $model
        ]);
    }

    /**
     * 返回模型
     *
     * @param
     *            $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = $this->modelClass::findOne($id)))) {
            $model = new $this->modelClass();
            return $model->loadDefaultValues();
        }
        
        return $model;
    }
}