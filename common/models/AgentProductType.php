<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\product\ProductType;
use common\models\user\UserSettlement;

class AgentProductType extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%agent_product_type}}';
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
                    'agent_id',
                    'product_type_id',
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'cash_money',
                    'frozen_money',
                    'capping'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'cash_money',
                    'capping'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ],
            [
                [
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'cash_money',
                    'capping'
                ],
                'double'
            ],
            [
                [
                    'return_days'
                ],
                'integer',
                'min' => 0,
                'max' => 50000
            ],
            [
                [
                    'return_order_total_money',
                ],
                'double',
                'min' => 0,
                'max' => 99999999
            ],
            [
                [
                    'return_rewards_money',
                ],
                'double',
                'min' => 0,
                'max' => 999999
            ],
//            [
//                [
//                    'level_cc_date',
//                    'level_dc_date',
//                    'cash_money_date'
//                ],
//                'date',
//                'format' => 'yyyy-mm-dd'
//            ]
        
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agent_id' => '代理商ID',
            'product_type_id' => '机具类型名称',
            'level_cc_settlement' => '本级贷记卡结算价',
            'level_cc_date' => '贷记卡结算价生效日期',
            'level_dc_settlement' => '本级借记卡结算价',
            'capping' => '借记卡封顶结算价',
            'level_dc_date' => '借记卡结算价生效日期',
            'cash_money' => '本级返现单价',
            'cash_money_date' => '返现单价生效日期',
            'frozen_money' => '到期冻结金额',
            'update_user' => '联系电话',
            'add_user' => '添加人',
            'updated_at' => '修改时间',
            'created_at' => '添加时间'
        ];
    }

    public function getProductType()
    {
        return $this->hasOne(ProductType::className(), [
            'id' => 'product_type_id'
        ]);
    }

    public function getUserSettlement()
    {
        return $this->hasOne(UserSettlement::className(), [
            'agent_product_type_id' => 'id'
        ]);
    }
}