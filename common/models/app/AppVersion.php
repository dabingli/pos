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
class AppVersion extends ActiveRecord
{

    const STOP = 2;

    const START = 1;

    const YES = 2;

    const NO = 1;

    const IOS = 2;

    const Andorid = 1;

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
        return '{{%app_version}}';
    }

    /**
     * 版本号是否启用
     */
    static public function StatusLabels()
    {
        return [
            self::START => '启用',
            self::STOP => '停用'
        ];
    }

    static public function AllowUpdateLabels()
    {
        return [
            self::YES => '是',
            self::NO => '否'
        ];
    }

    static public function TypeLabels()
    {
        return [
            self::IOS => 'IOS',
            self::Andorid => '安卓'
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'type',
                    'status',
                    'url',
                    'version',
                    'is_allow_update',
                    'create_name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'description'
                ],
                'safe'
            ]
        
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'type' => 'app类型',
            'description' => '功能描述',
            'url' => '下载地址',
            'is_allow_update' => '是否强制更新',
            'status' => '状态',
            'created_at' => '创建时间',
            'create_name' => '创建人'
        
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

    public function getAllowUpdate()
    {
        $statusLabels = self::AllowUpdateLabels();
        return isset($statusLabels[$this->is_allow_update]) ? $statusLabels[$this->is_allow_update] : '';
    }

    public function getType()
    {
        $statusLabels = self::TypeLabels();
        return isset($statusLabels[$this->type]) ? $statusLabels[$this->type] : '';
    }
}
