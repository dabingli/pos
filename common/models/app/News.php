<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class News extends ActiveRecord
{

    const DELETE = 1;

    const NOT_DELETE = 0;

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
        return '{{%news}}';
    }

    public function rules()
    {
        return [
            
            [
                [
                    'title',
                    'content',
                    'images'
                ],
                'safe'
            ],
            
            [
                [
                    'title',
                    'content'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'is_delete',
                'default',
                'value' => self::NOT_DELETE
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'content' => '内容',
            'images' => '图片',
            'title' => '内容名称',
            'created_at' => '创建时间'
        ];
    }
}