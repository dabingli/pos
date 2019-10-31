<?php
namespace backend\modules\app\controllers;

use yii;
use yii\helpers\Url;
use common\helpers\FormHelper;
use backend\modules\app\controllers\BaseController;
use common\models\app\News;

class NewsController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = News::find();
        $model->andWhere([
            'is_delete' => News::NOT_DELETE
        ]);
        $data = [];
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $message = $model->all();
        foreach ($message as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'title' => $val->title,
                'images' => json_decode($val->images, true),
                'content' => $val->content,
                'created_at' => date('Y-m-d H:i:s', $val->created_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('add');
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        $model = new News();
        $model->load([
            'content' => $this->request->post('content'),
            'title' => $this->request->post('title'),
            'images' => ! empty($this->request->post('image')) ? json_encode($this->request->post('image')) : json_encode([])
        ], '');
        if (! $model->save()) {
            return $this->redirect($this->message($this->multiErrors2Msg($model->errors), Url::toRoute('index')));
        } else {
            return $this->redirect($this->message('添加成功', Url::toRoute('index')));
        }
    }

    public function actionDel()
    {
        if (! News::updateAll([
            'is_delete' => News::DELETE
        ], [
            'id' => $this->request->post('id')
        ])) {
            $this->message('删除失败', Url::toRoute('index'));
        } else {
            $this->message('删除成功', Url::toRoute('index'));
        }
        return $this->asJson([]);
    }

    public function actionEdit()
    {
        $model = News::findOne([
            'id' => $this->request->post('id')
        ]);
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('edit', [
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        $model = News::findOne([
            'id' => $this->request->post('id')
        ]);
        $model->load([
            'content' => $this->request->post('content'),
            'title' => $this->request->post('title'),
            'images' => ! empty($this->request->post('image')) ? json_encode($this->request->post('image')) : json_encode([])
        ], '');
        if (! $model->save()) {
            return $this->redirect($this->message($this->multiErrors2Msg($model->errors), Url::toRoute('index')));
        } else {
            return $this->redirect($this->message('修改成功', Url::toRoute('index')));
        }
    }
}