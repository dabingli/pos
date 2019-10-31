<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;

class AuthenticationForm extends Model{

    public $real_name;
    public $identity;
    public $identity_front_images;
    public $identity_back_images;
    public $identity_personal_images;
    public $hold_identity_images;

    public function rules()
    {
        return [
          [
              [
                  'real_name',
                  'identity',
                  'identity_front_images',
                  'identity_back_images',
                  'identity_personal_images',
                  'hold_identity_images',
              ],
              'required',
              'message' => '{attribute}不能为空'
          ],
            [
                [
                    'identity'
                ],
                'match',
                'pattern' => '/^\d{17}[0-9xX]$/',
                'message' => '身份证格式不正确'
            ]

        ];
    }

    public function attributeLabels()
    {
        return [
            'real_name' => '名字',
            'identity' => '身份证',
            'identity_front_images' => '身份证正面图片',
            'identity_back_images' => '身份证背面图片',
            'identity_personal_images' => '个人自拍照',
            'hold_identity_images' => '手持身份证照片'
        ];
    }

//    实名认证
    public function authentication(User $user)
    {
        if (! $this->validate()) {
            return null;
        }
        $images = [
            'identity_front_images' => $this->identity_front_images,
            'identity_back_images' => $this->identity_back_images,
            'identity_personal_images' => $this->identity_personal_images,
            'hold_identity_images' => $this->hold_identity_images,
        ];
        $user->auth_images = json_encode($images);
        $user->is_authentication = 2;
        $user->real_name = $this->real_name;
        $user->identity = $this->identity;
        if($user->save())
        {
            return true;
        }else{
            $this->addErrors($user->getFirstErrors());
            return null;
        }
    }
}
