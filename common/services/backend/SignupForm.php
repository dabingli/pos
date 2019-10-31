<?php
namespace common\models\services\backend;

use yii\base\Model;
use common\models\entities\AdminUser;

class SignupForm extends Model
{

    public $user_name;

    public $password;

    /**
     *
     * {@inheritdoc}
     *
     */
    public function rules()
    {
        return [
            [
                'user_name',
                'trim'
            ],
            [
                'user_name',
                'required'
            ],
            [
                'user_name',
                'unique',
                'targetClass' => '\common\models\entities\AdminUser',
                'message' => '该用户名已存在'
            ],
            [
                'user_name',
                'string',
                'min' => 5
            ],
            
            [
                'password',
                'required'
            ],
            [
                'password',
                'string',
                'min' => 6
            ]
        ];
    }

    public function signup()
    {
        if (! $this->validate()) {
            return null;
        }
        $user = new AdminUser();
        $user->user_name = $this->user_name;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }

    public function attributeLabels()
    {
        return [
            'user_name' => '用户名',
            'password' => '密码'
        ];
    }
}
