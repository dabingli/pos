<?php
namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


class UserIdentityAudit extends ActiveRecord
{

    const IDENTITY = 1;

    const AUDIT = 1;

    const NOT_PASS = 2;

    const PASS = 3;

    public $identity_front_images;

    public $identity_back_images;

    public $identity_personal_images;

    public $hold_identity_images;

    public $code;


    public static function getTypeLabels()
    {
        return [
            self::IDENTITY => '身份证',
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::AUDIT => '审核中',
            self::NOT_PASS => '不通过',
            self::PASS => '通过'
        ];
    }

    public function getType(){
        $type = self::getTypeLabels();
        return $type[$this->type];
    }

    public function getStatus(){
        $status = self::getStatusLabels();
        return $status[$this->status];
    }

    public function rules(){
        return [
            [
                'image',
                'validateImage'
            ],
            [
                [
                    'image',
                    'real_name',
                    'code',
                    'identity_card',
                    'cardNo'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'agent_id',
                    'user_id',
                    'real_name',
                    'identity_card',
                    'cardNo',
                    'type',
                    'image',
                    'status'
                ],
                'safe'
            ],
            [
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/real/authentication-code',
                'message' => '验证码错误'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'identity_front_images' => '身份证正面图片',
            'identity_back_images' => '身份证背面图片',
            'identity_personal_images' => '个人自拍照',
            'hold_identity_images' => '手持身份证图片',
            'image' => '图片',
            'real_name' => '姓名',
            'identity_card' => '身份证号码',
            'cardNo' => '银行卡号'
        ];
    }

    /**
     *
     * @return array
     */
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

    public function getUser(){
        return $this->hasOne(User::className(),[
            'id' => 'user_id'
        ]);
    }

    public function validateImage($attribute)
    {
        $image_arr = json_decode($this->image,true);

        if(empty($image_arr['identity_front_images'])){
            $this->addError($attribute, '身份证正面图片不能为空');
            return false;
        }
        if(empty($image_arr['identity_back_images'])){
            $this->addError($attribute, '身份证背面图片不能为空');
            return false;
        }
        if(empty($image_arr['identity_personal_images'])){
            $this->addError($attribute, '个人自拍照不能为空');
            return false;
        }
        if(empty($image_arr['hold_identity_images'])){
            $this->addError($attribute, '手持身份证图片不能为空');
            return false;
        }
        return true;
    }
}