<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\user\User;

class UserMessage extends ActiveRecord
{

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%user_message}}';
    }

    public function rules()
    {
        return [
            
            [
                [
                    'app_message_id',
                    'read',
                    'user_id'
                ],
                'safe'
            ],
            [
                'read',
                'default',
                'value' => 0
            ],
            [
                [
                    'app_message_id',
                    'user_id',
                    'type'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function getApp()
    {
        return $this->hasOne(AppSettings::className(), [
            'id' => 'app_id'
        ]);
    }

    public function attributeLabels()
    {
        return [
            'app_message_id' => '消息ID',
            'read' => '是否已读',
            'user_id' => '用户ID'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'user_code' => 'user_code'
        ]);
    }

    public function getAppMessage()
    {
        return $this->hasOne(AppMessage::className(), [
            'id' => 'app_message_id'
        ]);
    }
}