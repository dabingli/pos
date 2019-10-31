<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\UploadedFileBehavior;

/**
 * App管理
 *
 * @author Administrator
 *        
 */
class AppShare extends ActiveRecord
{

    const STOP = 2;

    const START = 1;

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

    public function getApp()
    {
        return $this->hasOne(AppSettings::className(), [
            'id' => 'app_id'
        ]);
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    static public function StatusLabels()
    {
        return [
            self::START => '启用',
            self::STOP => '停用'
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'description',
                    'created_at',
                    'app_id'
                ],
                'safe'
            ],
            [
                [
                    'status',
                    'sort',
                    'add_name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'image'
                ],
                'required',
                'message' => '请先上传{attribute}'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'status' => '状态',
            'description' => '图片说明',
            'sort' => '排序',
            'image' => '图片',
            'add_name' => '创建人',
            'created_at' => '创建时间'
        
        ];
    }

    public static function tableName()
    {
        return '{{%app_share}}';
    }
}