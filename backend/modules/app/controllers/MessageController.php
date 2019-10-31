<?php
namespace backend\modules\app\controllers;

use yii;
use yii\helpers\Url;
use common\helpers\FormHelper;
use common\models\app\AppMessage;
use backend\modules\app\controllers\BaseController;
use common\components\queue\job\AppMessage as AppMessageQueue;

class MessageController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = AppMessage::find();
        $model->with('app');
        $data = [];
        $data['total'] = $model->count();
        $model->orderBy(['created_at'=>SORT_DESC]);
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $message = $model->all();
        foreach ($message as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'app_name' => $val->app->name,
                'type' => $val->getType(),
                'content' => $val->content,
                'receiver_name' => $val->receiver_name == AppMessage::ALL ? '全部' : '个推',
                'send_name' => $val->user_code,
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
        $model = new AppMessage();
        $model->load([
            'content' => $this->request->post('content'),
            'title' => $this->request->post('title'),
            'type' => $this->request->post('type'),
            'receiver_name' => $this->request->post('receiver_name'),
            'user_code' => ! $this->request->post('receiver_name') ? $this->request->post('user_code') : ''
        ], '');
        if (! $model->save()) {
            return $this->redirect($this->message($this->multiErrors2Msg($model->errors), Url::toRoute('index')));
        } else {
            Yii::$app->queue->push(new AppMessageQueue([
                'appMessageId' => $model->id
            ]));

            return $this->redirect($this->message('添加成功', Url::toRoute('index')));
        }
    }
}