<?php
namespace frontend\modules\sys\models;

use Yii;
use yii\base\Model;
use common\models\agent\AuthAssignment;
use common\models\agent\AuthItem;
use common\models\agent\AgentUser as Manager;
use yii\web\NotFoundHttpException;

class ManagerForm extends Model
{

    public $id;

    public $root;

    public $password;

    public $account;

    public $auth_key;

    public $agent_id;

    public $user_name;

    public $number;

    public $mobile;

    public $mailbox;

    public $remarks;

    public $add_user_name;

    /**
     *
     * @var \common\models\sys\Manager
     */
    protected $managerModel;

    /**
     *
     * @var \common\models\sys\AuthAssignment
     */
    protected $authAssignment;

    /*
     * @var \common\models\sys\AuthItem
     */
    protected $authItemModel;

    /**
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'password',
                    'account',
                    'user_name',
                    'mobile',
                    'auth_key',
                    'mailbox'
                ],
                'required'
            ],
            [
                'password',
                'string',
                'min' => 6
            ],
            [
                [
                    'auth_key'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuthItem::class,
                'targetAttribute' => [
                    'auth_key' => 'key'
                ]
            ],
            [
                [
                    'account'
                ],
                'isUnique'
            ],
            [
                [
                    'number',
                    'remarks',
                    'add_user_name'
                ],
                'default',
                'value' => ''
            ]
        ];
    }

    /**
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => '登录密码',
            'account' => '登录帐号',
            'agent_id' => '机构名称',
            'user_name' => '用户名',
            'number' => '工号',
            'mobile' => '手机号码',
            'auth_key' => '角色',
            'remarks' => '备注',
            'mailbox' => '联系邮箱'
        ];
    }

    /**
     * 加载默认数据
     */
    public function loadData()
    {
        $this->managerModel = Manager::findOne([
            'id' => $this->id,
            'agent_id' => $this->agent_id
        ]);
        if ($this->managerModel) {
            $this->account = $this->managerModel->account;
            $this->password = $this->managerModel->password_hash;
            $this->root = $this->managerModel->root;
            $this->user_name = $this->managerModel->user_name;
            $this->number = $this->managerModel->number;
            $this->mobile = $this->managerModel->mobile;
            $this->mailbox = $this->managerModel->mailbox;
            $this->remarks = $this->managerModel->remarks;
        } else {
            $this->managerModel = new Manager();
        }
        
        $this->authAssignment = AuthAssignment::find()->where([
            'user_id' => $this->id
        ])
            ->with('itemName')
            ->one();
        
        if ($this->authAssignment) {
            $this->auth_key = $this->authAssignment->itemName->key;
        } else {
            $this->authAssignment = new AuthAssignment();
        }
    }

    /**
     * 场景
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => [
                'account',
                'password',
                'user_name',
                'number',
                'mobile',
                'remarks',
                'mailbox'
            ],
            'generalAdmin' => array_keys($this->attributeLabels())
        ];
    }

    /**
     * 验证用户名称
     */
    public function isUnique()
    {
        $manager = Manager::findOne([
            'account' => $this->account
        ]);
        if ($manager && $manager->id != $this->id) {
            $this->addError('username', '用户名称已经被占用');
        }
    }

    /**
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveData()
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $manager = $this->managerModel;
            $manager->account = $this->account;
            
            // 验证密码是否修改
            if ($this->managerModel->password_hash != $this->password) {
                $manager->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            }
            $manager->user_name = $this->user_name;
            $manager->number = $this->number;
            $manager->mobile = $this->mobile;
            $manager->mailbox = $this->mailbox;
            $manager->remarks = $this->remarks;
            $manager->agent_id = $this->agent_id;
            if (! $manager->save()) {
                $this->addErrors($manager->getErrors());
                throw new NotFoundHttpException('用户编辑错误');
            }
            
            // 验证超级管理员
            if ($this->root == Manager::ROOT) {
                $transaction->commit();
                return true;
            }
            
            $authAssignment = $this->authAssignment;
            $authAssignment->user_id = $manager->id;
            $authAssignment->item_id = (AuthItem::findOne([
                'key' => $this->auth_key
            ]))->id;
            if (! $authAssignment->save()) {
                $this->addErrors($authAssignment->getErrors());
                throw new NotFoundHttpException('权限写入错误');
            }
            
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}