<?php
namespace common\models\product;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ProductType extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%product_type}}';
    }

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

    public function rules()
    {
        return [
            [
                [
                    'name',
                    /*'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',*/
                    'activation_money'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    /*'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',*/
                    'activation_money'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ],
            [
                [
                    'add_user'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '代理商名称',
            'add_user' => '添加人',
            'updated_at' => '修改时间',
            'created_at' => '添加时间'
        ];
    }
}