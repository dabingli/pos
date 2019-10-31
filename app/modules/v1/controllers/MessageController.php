<?php
namespace app\modules\v1\controllers;

use yii;
use yii\filters\auth\HttpBearerAuth;
use common\models\user\User;
use common\models\app\AppMessage;
use common\models\app\UserMessage;

class MessageController extends BaseActiveController
{

    public $modelClass = 'common\models\app\AppMessage';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className()
        ];
        
        return $behaviors;
    }

    public function actionList()
    {
        $model = UserMessage::find();
        $model->andWhere([
            'user_id' => Yii::$app->user->id,
            'type' => $this->request->post('type')
        ]);
        $limit = $this->request->post('limit') ? $this->request->post('limit') : 10;
        $offset = (($this->request->post('page') > 1 ? $this->request->post('page') : 1) - 1) * $limit;
        $model->limit($limit);
        $model->offset($offset);
        $model->orderBy([
            'read' => SORT_ASC,
            'id' => SORT_DESC
        ]);
        $model->with('appMessage');
        $model->asArray();
        $data = $model->all();

        foreach ($data as $k=>$v){
            $data[$k]['user_message_id'] = $v['id'];
            $data[$k]['id'] = $v['app_message_id'];
        }

        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }

    public function actionFind()
    {
        $userMessage = UserMessage::findOne([
            'app_message_id' => $this->request->post('id'),
            'user_id' => Yii::$app->user->id
        ]);
        $model = AppMessage::findOne([
            'id' => $userMessage['app_message_id']
        ]);

        if(!is_null($userMessage) && $userMessage['read'] != 1){
            $userMessage->load([
                'read' => 1
            ], '');
            $userMessage->save();
        }

        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $model
        ];
    }

    /**
     * 一键已读
     *
     * @return number[]|string[][]|\common\models\app\UserMessage[]|NULL[]
     */
    public function actionRead()
    {
        UserMessage::updateAll([
            'read' => 1
        ], [
            'read' => 0,
            'user_id' => Yii::$app->user->id
        ]);
        return [
            'status' => 0,
            'code' => 200,
            'message' => [
            ],
            'data' => []
        ];
    }
}