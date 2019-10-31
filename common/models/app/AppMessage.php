<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\user\User;

/**
 * App管理
 *
 * @author Administrator
 *        
 */
class AppMessage extends ActiveRecord
{

    const SYSTEM = 1;

    const NOTICE = 2;

    const PROFIT = 4;

    const ACTIVATE = 3;

    const OTHER = 5;

    const ALL = 1;

    const NOT_ALL = 0;

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
        return '{{%app_message}}';
    }

    public function rules()
    {
        return [
            
            [
                [
                    'app_id',
                    'content',
                    'receiver_name',
                    'user_code',
                    'title',
                    'type'
                ],
                'safe'
            ],
            
            [
                [
                    'content',
                    'title',
                    'type'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'receiver_name',
                function ($attribute) {
                    if ($this->receiver_name == self::NOT_ALL) {
                        if (empty($this->user_code)) {
                            $this->addError($attribute, '推送人不能为空');
                            return false;
                        }
                    }
                    return true;
                }
            ]
        ];
    }

    public function beforeSave($insert)
    {
        $this->app_id = 1;
        if ($insert) {
            if ($this->receiver_name == self::NOT_ALL && empty($this->user)) {
                $this->addError('user_code', '推送人不能为空');
                return false;
            }
        }
        return parent::beforeSave($insert);
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
            'title' => '消息名称',
            'app_id' => '主键APP_ID',
            'type' => '消息类型',
            'content' => '消息内容',
            'receiver_name' => '被推送群体',
            'created_at' => '创建时间',
            'user_code' => '推送人'
        ];
    }

    static public function TypeLabels()
    {
        return [
            self::SYSTEM => '系统',
            self::NOTICE => '公告',
            self::ACTIVATE => '激活',
            self::PROFIT => '分润',
            self::OTHER => '其他'
        ];
    }

    public function getType()
    {
        $statusLabels = self::TypeLabels();
        return isset($statusLabels[$this->type]) ? $statusLabels[$this->type] : '';
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'user_code' => 'user_code'
        ]);
    }
}