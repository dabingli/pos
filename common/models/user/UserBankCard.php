<?php
namespace common\models\user;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\user\BankCard;

class UserBankCard extends ActiveRecord
{

    // // 允许最多能添加的信用卡数量
    // const LIST_CC_YES = 5;
    //
    // // 允许最多能添加的储存卡数量
    // const LIST_DC_YES = 5;
    //
    // const DELETE = 1;
    //
    const NOT_DELETE = 0;

    const DEFAULT = 1;

    //
    // const NOT_DEFAULT = 0;
    //
    // const CC_TYPE = 'CC';
    //
    // const DC_TYPE = 'DC';
    //
    // static public function typeLabels()
    // {
    // return [
    // self::CC_TYPE => '信用卡',
    // self::DC_TYPE => '储存卡'
    // ];
    // }
    //
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

    //
    // public function rules()
    // {
    // return [
    // [
    // [
    // 'cardNo'
    // ],
    // 'safe'
    // ],
    // [
    // [
    //
    // 'agent_id',
    // 'user_id',
    // 'cardNo',
    // 'bank',
    // 'type',
    // 'app_id',
    // 'identity',
    // 'bank_account_name'
    // ],
    // 'required',
    // 'message' => '{attribute}不能为空'
    // ],
    // [
    // 'cardNo',
    // function ($attribute) {
    // if (self::findOne([
    // 'cardNo' => $this->cardNo,
    // 'is_delete' => self::NOT_DELETE,
    // 'user_id' => $this->user_id
    // ])) {
    // $this->addError($attribute, '该银行卡已添加');
    // return false;
    // }
    //
    // return true;
    // }
    // ],
    // [
    // 'is_delete',
    // 'in',
    // 'range' => [
    // self::DELETE,
    // self::NOT_DELETE
    // ]
    // ],
    // [
    // 'is_delete',
    // 'default',
    // 'value' => self::NOT_DELETE
    // ]
    // ];
    // }
    //
    // public static function tableName()
    // {
    // return '{{%user_bank_card}}';
    // }
    //
    // public function attributeLabels()
    // {
    // return [
    // 'app_id' => 'APP_ID',
    // 'agent_id' => '商家ID',
    // 'user_id' => '用户ID',
    // 'cardNo' => '银行卡号',
    // 'bank' => '银行',
    // 'type' => '类型',
    // 'identity' => '身份证号码',
    // 'bank_account_name' => '银行开户名称',
    // 'bank_branches' => '银行分行',
    // 'bank_phone' => '开户预留手机号',
    // 'is_default' => '是否默认',
    // 'is_delete' => '是否删除'
    // ];
    // }
    //
    // public function getSensitiveCardNo()
    // {
    // $len = strlen($this->cardNo);
    // $str = substr($this->cardNo, 4, $len - 8);
    // $s = '';
    // for ($i = 0; $i < 8; $i ++) {
    // $s .= '*';
    // }
    // return str_replace($str, $s, $this->cardNo);
    // }
    //
    public function getBankCard()
    {
        return $this->hasOne(BankCard::className(), [
            'bank' => 'bank'
        ]);
    }
}