<?php
namespace common\components\queue\job;

use yii;
use yii\queue\JobInterface;
use common\models\app\AppMessage as AppMessageModel;
use common\models\app\UserMessage;
use common\models\user\User;
use common\services\common\AppMessage as AppMessageService;

class AppMessage extends BaseObject implements JobInterface
{

    public $appMessageId;

    public function execute($queue)
    {
        $model = AppMessageModel::findOne([
            'id' => $this->appMessageId
        ]);
        
        if ($model->receiver_name == AppMessageModel::NOT_ALL) {
            $userMessage = new UserMessage();
            $userMessage->load([
                'app_message_id' => $this->appMessageId,
                'user_id' => $model->user->id,
                'type' => $model->type
            ], '');
            $userMessage->save();
        } else {
            $user = User::find();
            $user->select([
                'id'
            ]);
            foreach ($user->all() as $m) {
                $userMessage = new UserMessage();
                $userMessage->load([
                    'app_message_id' => $this->appMessageId,
                    'user_id' => $m->id,
                    'type' => $model->type
                ], '');
                $userMessage->save();
            }
        }
        
        (new AppMessageService($this->appMessageId))->send();
    }
}