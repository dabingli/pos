<?php
namespace common\services\common;

use Yii;
use common\services\Service;
use common\models\user\User;
use common\models\app\AppMessage as AppMessageModel;
use common\library\uni_push\Notification;
use common\library\uni_push\SingleNotification;

class AppMessage extends Service
{
    public $message;

    public function __construct($appMessageId)
    {
        parent::__construct();

        $this->message = AppMessageModel::findOne(['id'=>$appMessageId]);
    }

    /**
     * @sendNotification 根据appMessageId发送通知
     * @return bool|mixed|null
     */
    public function send()
    {
        if(empty($this->message['user_code'])){

            return $this->sendAll();

        } else {

            $user = User::findOne(['user_code'=>$this->message['user_code']]);

            return $this->sendSingleByClientId($user['client_id']);
        }
    }

    /**
     * @sendAll 群发通知
     * @return mixed|null
     */
    public function sendAll()
    {
        $notificationModel = new Notification();
        $notificationModel->load([
            'title' => $this->message['title'],
            'content' => mb_substr($this->message['content'], 0, 50),
            'payload' => ['id'=>$this->message['id'], 'type'=>$this->message['type']]
        ], '');
        return $notificationModel->send();
    }

    /**
     * @sendSingle 根据userId发送通知
     * @param $userId
     * @return bool|mixed|null
     */
    public function sendSingleByUserId($userId)
    {
        $user = User::findOne(['id'=>$userId]);

        return $this->sendSingleByClientId($user['client_id']);
    }

    /**
     * @sendSingle 根据clientid用户发送通知
     * @param $clientId
     * @return bool|mixed|null
     */
    public function sendSingleByClientId($clientId)
    {
        if(!empty($clientId)){
            $singleNotificationModel = new SingleNotification();
            $singleNotificationModel->load([
                'clientId' => $clientId,
                'title' => $this->message['title'],
                'content' => mb_substr($this->message['content'], 0, 50),
                'payload' => ['id'=>$this->message['id'], 'type'=>$this->message['type']],
            ], '');

            return $singleNotificationModel->send();
        }

        return false;
    }

}