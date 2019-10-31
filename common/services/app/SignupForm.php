<?php
namespace common\services\app;

use yii;
use yii\base\Model;
use common\models\user\User;
use common\models\agent\Agent;
use common\helpers\RegularHelper;
use common\models\user\nestedSets\UserLink;

/**
 * 注册
 *
 * @author zhouchen
 *        
 */
class SignupForm extends Model
{

    public $mobile;

    public $agent_id;

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
                    'user_name',
                    'parent_user',
                    'code'
                ],
                'required',
                'message' => '{attribute}不能为空'
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
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/user/signup-code',
                'message' => '验证码错误'
            ],
            [
                [
                    'mobile',
                    'parent_user'
                ],
                'match',
                'pattern' => RegularHelper::mobile(),
                'message' => '手机号码格式错误'
            ],
            [
                'parent_user',
                function ($attribute) {
                    
                    $this->parentUserModel = User::findOne([
                        'mobile' => $this->parent_user
                    ]);
                    
                    if (empty($this->parentUserModel)) {
                        $this->addError($attribute, '上级代理商不能为空');
                        return false;
                    }
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
                    'parent_user'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'agent_id' => '商家ID',
            'mobile' => '手机号',
            'password' => '登录密码',
            'parent_user' => '上级代理商',
            'user_name' => '后台名称',
            'code' => '验证码'
        ];
    }

    public function signup()
    {
        if (! $this->validate()) {
            return null;
        }
        $user = new User();
        $data = $this->toArray();
        $data['agent_id'] = $this->parentUserModel->agent_id;
        $data['parent_id'] = isset($this->parentUserModel->id) ? $this->parentUserModel->id : 0;
        $user->load($data, '');
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        if (! $user->save()) {
            $this->addErrors($user->errors);
            return null;
        }
        $this->agent_id = $this->parentUserModel->agent_id;
        $userLinkModel = new UserLink([
            'user_id' => $user->id,
            'agent_id' => $this->agent_id
        ]);
        if (! $userLinkModel->prependTo(UserLink::findOne([
            'user_id' => $data['parent_id']
        ]))) {
            $this->addErrors([
                'id' => '用户添加失败'
            ]);
            return null;
        }
        return $user;
    }
}
