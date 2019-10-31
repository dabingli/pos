<?php
namespace common\models\user;

use common\helpers\RegularHelper;
use common\models\agent\AgentUser;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use common\models\agent\Agent;
use common\helpers\Sensitive;
use common\models\user\nestedSets\UserLink;
use common\models\user\UserSettlement;
use common\models\TransactionTotal;

/**
 * 用户表
 *
 * @author zhouchen
 *        
 */
class User extends ActiveRecord implements IdentityInterface
{

    // 正常
    const OPEND_STATUS = 1;

    // 禁用
    const CLOSE_STATUS = 0;

    // 已实名验证
    const AUTH_YES = 1;

    // 未实名验证
    const AUTH_NOT = 0;

    const SUBMISSION = 2;

    const REGISTER = 1;

    const NOT_REGISTER = 0;

    const NOT_FROZEN_EARNINGS = 1;

    const FROZEN_EARNINGS = 2;

    const NOT_FROZEN_DISTRIBUTING = 1;

    const FROZEN_DISTRIBUTING = 2;

    public static function getAuthenticationLabels()
    {
        return [
            self::AUTH_YES => '是',
            self::AUTH_NOT => '否',
            self::SUBMISSION => '等待审核'
        ];
    }

    public static function getRegisterLabels()
    {
        return [
            self::REGISTER => '已登记',
            self::NOT_REGISTER => '未登记'
        ];
    }

    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, [
            $this,
            'updateUserPwd'
        ]);

        $this->on(self::EVENT_AFTER_UPDATE, [
            $this,
            'updateAgentPwd'
        ]);

        return parent::init();
    }

    public static function getStatusLabels()
    {
        return [
            self::OPEND_STATUS => '正常',
            self::CLOSE_STATUS => '禁用'
        ];
    }

    public function getRegister()
    {
        $getRegisterLabels = self::getRegisterLabels();
        return isset($getRegisterLabels[$this->register]) ? $getRegisterLabels[$this->register] : '';
    }

    public function getStatus()
    {
        $getStatusLabels = self::getStatusLabels();
        return isset($getStatusLabels[$this->status]) ? $getStatusLabels[$this->status] : '';
    }

    public function getAuthentication()
    {
        $getAuthenticationLabels = self::getAuthenticationLabels();
        return isset($getAuthenticationLabels[$this->is_authentication]) ? $getAuthenticationLabels[$this->is_authentication] : '';
    }

    /**
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [
                [
                    'client_id'
                ],
                'safe'
            ],
            [
                [
                    'user_name',
                    'mobile',
                    'password_hash',
                    'agent_id'
                
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'mobile',
                    'user_name'
                ],
                'filter',
                'filter' => 'trim',
                'skipOnArray' => true
            ],
            [
                'agent_id',
                function ($attribute) {
                    // $model = Agent::findOne($this->$attribute);
                    $model = $this->agent;
                    if (empty($model)) {
                        // 商家状态不做判断了，因为闲鱼被关闭后，用户还是照样用
                        $this->addError($attribute, '商家不存在');
                        return false;
                    }
                    return true;
                }
            ],
            [
                'mobile',
                'match',
                'pattern' => RegularHelper::mobile(),
                'message' => '手机号码格式错误'
            ],
            [
                'status',
                'in',
                'range' => [
                    self::CLOSE_STATUS,
                    self::OPEND_STATUS
                ]
            ],
            [
                [
                    'status',
                    'agent_id',
                    'is_authentication',
                    'parent_id',
                    'login_time'
                ],
                'integer'
            ],
            [
                'is_authentication',
                'in',
                'range' => [
                    self::AUTH_YES,
                    self::AUTH_NOT,
                    self::SUBMISSION
                ]
            ],
            [
                'is_authentication',
                'default',
                'value' => self::AUTH_NOT
            ],
            [
                'status',
                'default',
                'value' => self::OPEND_STATUS
            ],
            [
                [
                    'register'
                ],
                'default',
                'value' => self::NOT_REGISTER
            ],
            [
                'register',
                'in',
                'range' => array_keys(self::getRegisterLabels())
            ],
            [
                [
                    'identity',
                    'login_IP',
                    'address',
                    'opening_bank',
                    'bank_card',
                    'avatar'
                ],
                'default',
                'value' => ''
            ],
            [
                'parent_id',
                'default',
                'value' => 0
            ],
            [
                'frozen_earnings',
                'default',
                'value' => self::NOT_FROZEN_EARNINGS
            ],
            [
                'frozen_distributing',
                'default',
                'value' => self::NOT_FROZEN_DISTRIBUTING
            ],
            [
                'frozen_earnings',
                'in',
                'range' => [
                    self::NOT_FROZEN_EARNINGS,
                    self::FROZEN_EARNINGS
                ]
            ],
            [
                'frozen_distributing',
                'in',
                'range' => [
                    self::NOT_FROZEN_DISTRIBUTING,
                    self::FROZEN_DISTRIBUTING
                ]
            ],
            [
                'parent_id',
                function ($attribute) {
                    if ($this->$attribute > 0) {
                        $model = User::findOne([
                            'id' => $this->parent_id
                        ]);
                        if (empty($model) || $model->status != User::OPEND_STATUS) {
                            $this->addError($attribute, '推荐用户名不存在');
                            return false;
                        }
                    }
                    return true;
                }
            ],
        ];
    }

    public function getParent()
    {
        return $this->hasOne(User::className(), [
            'id' => 'parent_id'
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'user_code' => '商户编码',
            'mobile' => '手机号码',
            'user_name' => '用户名',
            'bank_card' => '银行卡',
            'opening_bank' => '开户行',
            'register' => '是否登记',
            'auth_key' => '自动登录key',
            'password_hash' => '加密密码',
            'password_reset_token' => '重置密码token',
            'email' => '邮箱',
            'agent_id' => '商家ID',
            'parent_id' => '父级ID',
            'client_id' => '客户端ID',
            'address' => '联系地址',
            'status' => '状态',
            'login_time' => '登录时间',
            'login_IP' => '登录IP',
            'is_authentication' => '是否实名认证',
            'real_name' => '真实姓名',
            'identity' => '身份证号码',
            'avatar' => '用户头像',
            'updated_at' => '修改时间',
            'created_at' => '添加时间'
        ];
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'id' => 'agent_id'
        ]);
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
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    public function setUserCode()
    {
        return 'M' . date('YmdHis') . rand(10000, 99999);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->user_code = $this->setUserCode();
            $this->generateAccessToken();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @ERROR!!!
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id
        ]);
    }

    /**
     * @ERROR!!!
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @ERROR!!!
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @ERROR!!!
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password
     *            password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password            
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString(200) . date('YmdHis');
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userId = Yii::$app->authenticator->getUser();
        return static::findIdentity($userId);
    }

    /**
     * 验证token是否过期
     * Validates if accessToken expired
     *
     * @param null $token            
     * @return bool
     */
    public static function validateAccessToken($token = null)
    {
        if ($token === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @return \yii\db\ActiveQuery 机具信息交易汇总
     */
    public function getTransactionTotal()
    {
        return $this->hasOne(TransactionTotal::className(), [
            'user_id' => 'id'
        ]);
    }

    public function getUserLink()
    {
        return $this->hasOne(UserLink::className(), [
            'user_id' => 'id'
        ]);
    }

    public function getUserSettlementOne()
    {
        return $this->hasOne(UserSettlement::className(), [
            'user_id' => 'id'
        ]);
    }

    public function getUserSettlementMany()
    {
        return $this->hasMany(UserSettlement::className(), [
            'user_id' => 'id'
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $model = new TransactionTotal();
            $model->load([
                'agent_id' => $this->agent_id,
                'user_id' => $this->id
            ], '');
            $model->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function updateUserPwd($event){
        $agent = AgentUser::findOne(['account'=>$event->sender->mobile]);
        if(!empty($agent) && $event->sender->password_hash != $agent->password_hash)
        {
            $event->sender->password_hash = $agent->password_hash;
            $event->sender->save();
        }
        return true;
    }

    public function updateAgentPwd($event){
        $agent = AgentUser::findOne(['account'=>$event->sender->mobile]);
        if(!empty($agent) && $event->sender->password_hash != $agent->password_hash)
        {
            $agent->password_hash = $event->sender->password_hash;
            $agent->save();
        }
        return true;
    }

    /**
     * @param $user
     * @param $post
     * @param string $fieldName
     * @param string $orderBy
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     *  代理已登记列表
     */
    public static function registeredList($user,$post, $fieldName='total_money', $orderBy='DESC', $page=0, $limit=10)
    {
        $index = $page * $limit;
        $month_start = date('Y-m-01 00:00:00');
        $month_end = date('Y-m-t 23:59:59');

        $user_name = !empty($post['user_name']) ? '%' . $post['user_name'] . '%' : '';

        $sql = "SELECT u.id,u.mobile,u.user_name,u.real_name,u.parent_id,u.is_authentication,u.agent_id,u.created_at,u.avatar ";
        $sql .= ", (SELECT SUM(t.txAmt) FROM user_link ll LEFT JOIN transaction t ON t.user_id = ll.user_id WHERE ll.tree = l.tree AND ll.lft >= l.lft AND ll.rgt <= l.rgt AND t.txDate >='" . $month_start . "' AND t.txDate <= '" . $month_end . "') AS {$fieldName} ";
        $sql .= ", (SELECT COUNT(m.id) FROM user_link ll LEFT JOIN merchant_user m ON m.user_id = ll.user_id WHERE ll.tree = l.tree AND ll.lft >= l.lft AND ll.rgt <= l.rgt) AS user_num";
        $sql .= ", (SELECT COUNT(ll.user_id) FROM user_link ll WHERE ll.tree = l.tree AND ll.lft >= l.lft AND ll.rgt <= l.rgt AND user_id != u.id) AS agent_num";
        $sql .= " FROM user u ";
//        $sql .= " LEFT JOIN merchant_user m ON u.id=m.user_id ";
//        $sql .= " LEFT JOIN transaction_total t ON u.id=t.user_id ";
        $sql .= " LEFT JOIN user_link l ON l.user_id=u.id ";
        $sql .= " WHERE u.parent_id = " . $user->id . " AND u.register = " . self::REGISTER;
        $sql .= " AND u.agent_id = {$user->agent_id} ";
        if(!empty($user_name))
        {
            $sql .= " AND (u.user_name like '" . $user_name . "' OR u.real_name like '".$user_name."')";
        }
        if(!empty($post['register_time_start']))
        {
            $sql .= " AND u.created_at >=" . strtotime($post['register_time_start'] . ' 00:00:00');
        }
        if(!empty($post['register_time_end']))
        {
            $sql .= " AND u.created_at <=" . strtotime($post['register_time_end'] . ' 23:59:59');
        }
        $sql .= " ORDER BY {$fieldName} {$orderBy}";
//        $sql .= " LIMIT {$index}, {$limit}";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}