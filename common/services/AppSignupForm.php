<?php
namespace common\models\services;

use yii;
use yii\base\Model;
use common\models\entities\User;
use common\models\entities\Agent;
use common\models\entities\nestedSets\UserLink;

/**
 * 注册
 *
 * @author zhouchen
 *        
 */
class AppSignupForm extends Model
{

    public $mobile;

    protected $agent_id;

    public $password;

    public $parent_user;

    public $code;

    public $user_name;

    protected $parentUserModel;

    /**
     * @ERROR!!!
     */
    public function rules()
    {
        return [
            [
                [
                    'mobile',
                    'password',
                    'code',
                    'user_name',
                    'parent_user'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'code',
                function ($attribute) {
                    if (! YII_ENV_PROD) {}
                    return true;
                }
            ],
            [
                'mobile',
                function ($attribute) {
                    $model = User::findOne([
                        'mobile' => $this->mobile
                    ]);
                    if (! empty($model)) {
                        $this->addError($attribute, '该用户已存在');
                        return false;
                    }
                    return true;
                }
            ],
            [
                [
                    'mobile',
                    'parent_user'
                ],
                'match',
                'pattern' => '/^[1][34578][0-9]{9}$/',
                'message' => '手机号码格式错误'
            ],
            [
                'parent_user',
                function ($attribute) {
                    
                    $this->parentUserModel = User::findOne([
                        'mobile' => $this->parent_user
                    ]);
                    
                    if (empty($this->parentUserModel)) {
                        $this->addError($attribute, '上级代理商不存在');
                        return false;
                    }
                    $this->agent_id = $this->parentUserModel->agent_id;
                    return true;
                }
            ],
            [
                'password',
                'string',
                'min' => 6
            ],
            [
                [
                    'user_name',
                    'password',
                    'parent_user',
                    'code'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '登录手机号',
            'password' => '密码',
            'parent_user' => '推荐人',
            'user_name' => '真实姓名',
            'code' => '验证码'
        ];
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    public function signup()
    {
        if (! $this->validate()) {
            return null;
        }
        $user = new User();
        $data = $this->toArray();
        $data['parent_id'] = isset($this->parentUserModel->id) ? $this->parentUserModel->id : 0;
        $data['agent_id'] = $this->agent_id;
        $user->load($data, '');
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        if (! $user->save()) {
            $this->addErrors($user->errors);
            return null;
        }
        
        $userLinkModel = new UserLink([
            'user_id' => $user->id,
            'agent_id' => $this->agent_id
        ]);
        
        if (! empty($data['parent_id'])) {
            if (! $userLinkModel->prependTo(UserLink::findOne([
                'user_id' => $data['parent_id']
            ]))) {
                $this->addErrors([
                    'id' => '用户添加失败'
                ]);
            }
        } else {
            if (! $userLinkModel->makeRoot()) {
                $this->addErrors([
                    'id' => '用户添加失败'
                ]);
                return null;
            }
        }
        
        return $user;
    }
}
