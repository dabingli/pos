<?php
namespace common\models\agent;

use common\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\agent\Agent;
use common\models\agent\AuthAssignment;

class AgentUser extends ActiveRecord implements IdentityInterface
{

    const STOP = 0;

    const START = 1;

    const ROOT = 1;

    const NOT_ROOT = 0;

    const MAN = 1;

    const WOMAN = 2;

    const UNKNOWN = 0;

    static public function findIdentity($id)
    {
        return self::findOne([
            'id' => $id
        ]);
    }

    public function init()
    {
        $this->on(self::EVENT_AFTER_UPDATE, [
            $this,
            'updatePwd'
        ]);

        return parent::init();
    }

    static public function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }

    public static function findByUsername($account)
    {
        return static::findOne([
            'account' => $account
        ]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(54) . time();
    }

    public static function tableName()
    {
        return '{{%agent_user}}';
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'user_name',
                    'agent_id',
                    'mobile',
                    'account'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'birthday'
                ],
                'safe'
            ],
            [
                [
                    'gender',
                    'provinces',
                    'city',
                    'area',
                    'visit_count',
                    'last_time'
                ],
                'integer'
            ],
            [
                'account',
                'unique'
            ],
            [
                'login_IP',
                'ip'
            ],
            [
                'login_time',
                'integer'
            ],
            [
                'login_IP',
                'ip'
            ],
            [
                'mailbox',
                'email'
            ],
            [
                'remarks',
                'string',
                'length' => [
                    0,
                    500
                ]
            ],
            [
                [
                    'number',
                    'remarks',
                    'add_user_name',
                    'address',
                    'head_portrait'
                ],
                'default',
                'value' => ''
            ],
            [
                'status',
                'default',
                'value' => self::START
            ],
            [
                'status',
                'in',
                'range' => array_keys(self::statusLabels())
            ],
            [
                'root',
                'in',
                'range' => [
                    self::ROOT,
                    self::NOT_ROOT
                ]
            ]
        ];
    }

    public static function statusLabels()
    {
        return [
            self::START => '启用',
            self::STOP => '停用'
        ];
    }

    public static function rootLabels()
    {
        return [
            self::ROOT => '是',
            self::NOT_ROOT => '否'
        ];
    }

    public function getRoot()
    {
        $rootLabels = self::rootLabels();
        return isset($rootLabels[$this->root]) ? $rootLabels[$this->root] : '';
    }

    public static function genderLabels()
    {
        return [
            self::UNKNOWN => '未知',
            self::MAN => '男',
            self::WOMAN => '女'
        ];
    }

    public function getGender()
    {
        $genderLabels = self::genderLabels();
        return isset($genderLabels[$this->gender]) ? $genderLabels[$this->gender] : '';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => '登录帐号',
            'agent_id' => '机构名称',
            'user_name' => '用户名',
            'number' => '工号',
            'mobile' => '手机号码',
            'login_IP' => '登录IP',
            'mailbox' => '联系邮箱',
            'login_time' => '登录时间',
            'status' => '状态',
            'updated_at' => '修改时间',
            'created_at' => '添加时间',
            'birthday' => '出生日期',
            'address' => '详细地址',
            'provinces' => '省份',
            'city' => '城市',
            'area' => '区',
            'gender' => '性别',
            'remarks' => '备注',
        ];
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'id' => 'agent_id'
        ]);
    }

    /**
     *
     * @return bool
     */
    public function beforeDelete()
    {
        AuthAssignment::deleteAll([
            'user_id' => $this->id
        ]);
        return parent::beforeDelete();
    }

    /**
     *
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        return parent::beforeSave($insert);
    }

    /**
     * 关联权限
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignment()
    {
        return $this->hasOne(AuthAssignment::class, [
            'user_id' => 'id'
        ]);
    }

    public function updatePwd($event)
    {
        $user = User::findOne(['mobile'=>$event->sender->account]);
        if(!empty($user) && $user->password_hash != $event->sender->password_hash)
        {
            $user->password_hash = $event->sender->password_hash;
            $user->save();
        }
        return true;
    }
}