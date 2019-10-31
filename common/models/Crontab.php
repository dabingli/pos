<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Crontab extends ActiveRecord
{

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%crontab}}';
    }

    public function rules()
    {
        return [
            [
                [
                    'name',
                    'route',
                    'remarks',
                    'crontab'
                ],
                'safe'
            ],
            [
                [
                    'name',
                    'route'
                ],
                'unique'
            ],
            [
                [
                    'name',
                    'route',
                    'crontab'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '任务名称',
            'route' => '任务操作',
            'remarks' => '备注',
            'crontab' => '时间格式'
        ];
    }
}