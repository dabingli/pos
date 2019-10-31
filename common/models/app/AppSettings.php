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
class AppSettings extends ActiveRecord
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
        return '{{%app_settings}}';
    }

    public function rules()
    {
        return [
            [
                [
                    'mobile'
                ],
                'safe'
            ],
            [
                [
                    'name',
                    'mobile'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键APP_ID',
            'name' => 'app名称',
            'mobile' => '客服电话',
            'create_name' => '创建人',
            'created_at' => '创建时间',
            'update_name' => '修改人',
            'updated_at' => '修改时间'
        ];
    }
}

