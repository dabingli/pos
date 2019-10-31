<?php
namespace common\models\agent;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\common\Provinces as Region;

class Agent extends ActiveRecord
{

    const STOP = 2;

    const START = 1;

    const CASH_STATUS_CLOSE = 2;

    const CASH_STATUS_OPEN = 1;

    public static function tableName()
    {
        return '{{%agent}}';
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
                    'number',
                    'contract_date',
                    'county_id',
                    'contacts',
                    'mobile',
                    'mailbox',
                    'address',
                    'admin_name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'number',
                'unique'
            ],
            [
                'contract_date',
                'date',
                'format' => 'yyyy-mm-dd'
            ],
            [
                [
                    'status',
                    'cash_status',
                    'manufactor_name',
                ],
                'safe'
            ],
            [
                'status',
                'default',
                'value' => self::START
            ],
            [
                'status',
                'in',
                'range' => array_keys(self::statusLabels())
            ],
            [
                [
                    'tax_point',
                    'min_cash_amount',
                    'cash_fee',
                    'cashback_fee',
                    'min_cashback',
                    'cashback_tax_point'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ],
        ];
    }

    public static function statusLabels()
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

    public function beforeSave($insert)
    {
        $model = Region::findOne([
            'id' => $this->county_id
        ]);
        if (! $model) {
            $this->addError('county_id', '城市县区错误');
            return false;
        }
        $this->city_id = $model->pid;
        $model = Region::findOne([
            'id' => $this->city_id
        ]);
        if (! $model) {
            $this->addError('city_id', '城市市错误');
            return false;
        }
        $this->province_id = $model->pid;
        if ($this->province_id <= 0) {
            $this->addError('province_id', '城市省份错误');
            return false;
        }
        
        return parent::beforeSave($insert);
    }

    public function getProvince()
    {
        return $this->hasOne(Region::className(), [
            'id' => 'province_id'
        ]);
    }

    public function getCity()
    {
        return $this->hasOne(Region::className(), [
            'id' => 'city_id'
        ]);
    }

    public function getCounty()
    {
        return $this->hasOne(Region::className(), [
            'id' => 'county_id'
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '代理商名称',
            'number' => '代理商编号',
            'contract_date' => '签约日期',
            'province_id' => '省ID',
            'city_id' => '市ID',
            'county_id' => '县ID',
            'status' => '状态',
            'contacts' => '联系人',
            'mobile' => '联系电话',
            'mailbox' => '联系邮箱',
            'address' => '联系地址',
            'updated_at' => '修改时间',
            'created_at' => '添加时间',
            'min_cashback' => '最小提现金额',
            'cashback_fee' => '提现手续费',
            'cash_fee' => '提现手续费',
            'min_cash_amount' => '最小提现金额',
            'tax_point' => '提现税点',
            'admin_name' => '代理商后台名称'
        ];
    }
}