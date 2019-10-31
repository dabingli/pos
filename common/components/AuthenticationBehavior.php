<?php
namespace common\components;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\InvalidCallException;

class AuthenticationBehavior extends Behavior
{

//    protected $agentId;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'authenticationValidate'
        ];
    }

    public function init()
    {
        parent::init();
    }

    public function authenticationValidate($event)
    {
        var_dump($this->owner);die;
        if ($this->owner->agent_id) {
            throw new InvalidCallException('该记录不存在');
        }
        return true;
    }
}