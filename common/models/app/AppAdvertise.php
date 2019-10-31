<?php
namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use common\components\UploadedFileBehavior;

/**
 * App管理
 *
 * @author Administrator
 *        
 */
class AppAdvertise extends ActiveRecord
{

    const ROLL_IMAGE = 1;

    const BIG_IMAGE = 2;

    const FEATURED_IMAGE = 3;//featured

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

    public static function tableName()
    {
        return '{{%app_advertise}}';
    }

    public function getApp()
    {
        return $this->hasOne(AppSettings::className(), [
            'id' => 'app_id'
        ]);
    }

    static public function TypeLabels()
    {
        return [
            self::ROLL_IMAGE => '首页轮播图',
            self::BIG_IMAGE => '首页大广告',
            self::FEATURED_IMAGE => 'App推荐位',
        ];
    }

    public function getType()
    {
        $statusLabels = self::TypeLabels();
        return isset($statusLabels[$this->type]) ? $statusLabels[$this->type] : '';
    }

    static public function StatusLabels()
    {
        return [
            self::START => '启用',
            self::STOP => '停用'
        ];
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    public function rules()
    {
        return [
            [
                [
                    'description',
                    'created_at',
                    'app_id',
                    'url'
                ],
                'safe'
            ],
            [
                [
                    'status',
                    'sort',
                    'add_name',
                    'type'
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
            'type' => '广告类型',
            'description' => '首页说明',
            'sort' => '排序',
            'image' => '图片',
            'add_name' => '创建人',
            'url' => '跳转地址',
            'created_at' => '创建时间'
        
        ];
    }
}