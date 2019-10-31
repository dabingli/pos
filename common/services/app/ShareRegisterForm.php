<?php
namespace common\services\app;

use common\models\user\nestedSets\UserLink;
use Yii;
use yii\base\Model;
use common\models\user\User;
use common\helpers\RegularHelper;

class ShareRegisterForm extends Model
{

    public $mobile;

    public $agent_id;

    public $password;

    public $repeat_password;

    public $code;

    public $user_name;

    public $parent_user;

    public $parent_user_id;

    protected $parentUserModel;

    public function rules()
    {
        return [
            [
                [
                    'mobile',
                    'password',
                    'user_name',
                    'repeat_password',
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
                'captchaAction' => 'site/register-code',
                'message' => '验证码错误'
            ],
            [
                [
                    'mobile',
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
                'password',
                'validatePassword'
            ],
            [
                [
                    'user_name',
                    'password',
                    'parent_user_id'
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
            'repeat_password' => '重复登录密码',
            'user_name' => '后台名称',
            'code' => '验证码'
        ];
    }

    public function validatePassword($attribute)
    {
        if($this->password != $this->repeat_password)
        {
            $this->addError($attribute, '两次输入的密码不一致');
            return false;
        }
    }

    public function signup()
    {
        if (! $this->validate()) {
            return null;
        }
        $user = new User();
        $data = $this->toArray();
        $data['agent_id'] = $this->parentUserModel->agent_id;
        $data['parent_id'] = $this->parent_user_id;
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