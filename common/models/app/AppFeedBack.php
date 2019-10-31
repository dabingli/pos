<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * App管理
 *
 * @author Administrator
 *        
 */
class AppFeedback extends ActiveRecord
{

    const PERFORM_PROBLEM = 1;

    const FUNCTION_PROBLEM = 2;

    const ALTERNATELY_PROBLEM = 3;

    const OTHER_PROBLEM = 4;

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
        return '{{%app_feedback}}';
    }

    public function getApp()
    {
        return $this->hasOne(AppSettings::className(), [
            'id' => 'app_id'
        ]);
    }

    public function rules()
    {
        return [
            [
                [
                    'type',
                    'description',
                    'app_id'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'created_at',
                    'name',
                    'agent_id'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '反馈类型',
            'app_id' => 'Appid',
            'description' => '问题描述',
            'name' => '创建人',
            'created_at' => '创建时间'
        ];
    }

    static public function TypeLabels()
    {
        return [
            self::PERFORM_PROBLEM => '性能问题',
            self::FUNCTION_PROBLEM => '功能问题',
            self::ALTERNATELY_PROBLEM => '交互问题',
            self::OTHER_PROBLEM => '其他问题'
        ];
    }

    public function getType()
    {
        $statusLabels = self::TypeLabels();
        return isset($statusLabels[$this->type]) ? $statusLabels[$this->type] : '';
    }
}