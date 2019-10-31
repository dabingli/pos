<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Region extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%region}}';
    }

    public function rules()
    {
        return [
            [
                [
                    'region_name',
                    'region_type',
                    'tel_code'
                ],
                'safe'
            ],
            [
                [
                    'parent_id',
                    'region_name',
                    'region_type',
                    'national_code',
                    'zip_code',
                    'sort_order'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'parent_id',
                    'region_type',
                    'national_code',
                    'sort_order'
                ],
                'integer'
            ],
            [
                [
                    'region_name'
                ],
                'string',
                'length' => [
                    1,
                    120
                ]
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'parent_id' => '父ID',
            'region_name' => '区域名称',
            'region_type' => '类型',
            'national_code' => 'national_code',
            'zip_code' => 'zip_code',
            'sort_order' => 'sort_order'
        ];
    }

    /**
     * 获得子城市
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRegionSon()
    {
        return $this->hasMany(Region::className(), [
            'parent_id' => 'region_id'
        ]);
    }

    /**
     * 获得父城市
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRegionParent()
    {
        return $this->hasOne(Region::className(), [
            'region_id' => 'parent_id'
        ]);
    }
}