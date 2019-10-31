<?php
namespace common\services;

use yii;
use yii\base\Model;
use common\models\agent\AgentUser as User;

class AgentSignupForm extends Model
{

    public $agent_id;

    public $account;

    public $user_name;

    public $mobile;

    public $mailbox;

    public $remarks;

    public $password;

    public $add_user_name;

    public $root = 0;

    /**
     *
     * {@inheritdoc}
     *
     */
    public function rules()
    {
        return [
            [
                [
                    'account',
                    'user_name',
                    'mobile',
                    'mailbox',
                    'remarks',
                    'add_user_name',
                ],
                'safe'
            ],
            [
                [
                    'agent_id',
                    'account',
                    'user_name',
                    'mobile',
                    'mailbox',
                    'remarks',
                    'password',
                    'add_user_name'
                ],
                'trim'
            ],
            [
                'account',
                'required'
            ],
            [
                'account',
                'unique',
                'targetClass' => '\common\models\agent\AgentUser',
                'message' => '该登录帐号已存在'
            ],
            [
                
                [
                    'account',
                    'user_name'
                ],
                'string',
                'length' => [
                    1,
                    32
                ]
            ],
            [
                'password',
                'string',
                'length' => [
                    6,
                    32
                ]
            ],
            [
                'mailbox',
                'email'
            ],
            [
                'root',
                'in',
                'range' => [
                    User::ROOT,
                    User::NOT_ROOT
                ]
            ]
        ];
    }

    public function signup()
    {
        if (! $this->validate()) {
            return null;
        }
        $user = new User();
        $load = $this->toArray();
        $user->load($load, '');
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }

    public function attributeLabels()
    {
        return [
            'agent_id' => '机构名称',
            'account' => '登录帐号',
            'user_name' => '用户名',
            'number' => '工号',
            'mobile' => '手机号码',
            'mailbox' => '联系邮箱',
            'remarks' => '备注',
            'password' => '登录密码',
        ];
    }
}
