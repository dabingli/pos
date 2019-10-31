<?php
namespace common\models\user;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class BankCard extends ActiveRecord
{

    // 正常
    const OPEND_STATUS = 1;

    // 禁用
    const CLOSE_STATUS = 0;

    public static function tableName()
    {
        return '{{%bank_card}}';
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    static public function statusLabels()
    {
        return [
            self::OPEND_STATUS => '正常',
            self::CLOSE_STATUS => '禁用'
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
                    'name',
                    'order',
                    'logo'
                ],
                'safe'
            ],
            [
                [
                    'bank',
                    'name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'logo'
                ],
                'file',
                'extensions' => [
                    'png',
                    'jpg',
                    'gif'
                ],
                'maxSize' => 1024 * 1024 * 2
            ],
            [
                'status',
                'in',
                'range' => [
                    self::CLOSE_STATUS,
                    self::OPEND_STATUS
                ]
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'bank' => '银行',
            'name' => '银行名称',
            'logo' => 'LOGO',
            'status' => '状态',
            'order' => '排序'
        ];
    }
}