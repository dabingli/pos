<?php
namespace common\services\app;

use Yii;
use yii\base\Model;
use common\models\user\User;

class EditInfoForm extends Model{

    public $avatar;
    public $email;
    public $address;

    public function rules()
    {
        return [
            [
                [
                    'avatar',
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'email',
                    'address'
                ],
                'safe'
            ],
            [
                [
                    'email'
                ],
                'match',
                'pattern' => '/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims',
                'message' => '邮箱格式不正确'
            ]

        ];
    }

    public function attributeLabels()
    {
        return [
            'avatar' => '头像',
            'email' => '邮件',
            'address' => '联系地址',

        ];
    }

//    编辑个人信息
    public function editInfo(User $user)
    {
        if (! $this->validate()) {
            return null;
        }
        $user->avatar = $this->avatar;
        if(!empty($this->email)){
            $user->email = $this->email;
        }
        if(!empty($this->address))
        {
            $user->address = $this->address;
        }

        if($user->save())
        {
            return true;
        }else{
            $this->addErrors($user->getFirstErrors());
            return null;
        }
    }
}
