<?php
namespace common\models\user;

use common\models\product\ProductType;
use Yii;
use yii\db\ActiveRecord;
use common\models\agent\AgentProductType;
use common\models\user\User;
use yii\behaviors\TimestampBehavior;

class UserSettlement extends ActiveRecord
{

    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, [
            $this,
            'register'
        ]);
        
        return parent::init();
    }

    public static function tableName()
    {
        return '{{%user_settlement}}';
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
                    'agent_id',
                    'user_id',
                    'agent_product_type_id',
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',
                    'cash_money'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'level_cc_settlement',
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',
                    'cash_money'
                ],
                'double'
            ],
            [
                [
                    'agent_id',
                    'user_id',
                    'agent_product_type_id',
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',
                    'cash_money'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ],
            'level_cc_settlement' => [
                [
                    'level_cc_settlement'
                ],
                function ($attribute) {
                    $sonUser = $this->maxLevelCcSettlement;
//                    var_dump($this->level_cc_settlement);die;
                    if (! empty($sonUser)) {
                        // 如果存在下级，
                        if ($sonUser->level_cc_settlement < $this->level_cc_settlement) {
                            $this->addError($attribute, '存在下级最低贷记卡结算价' . $sonUser->level_cc_settlement . '设置不能大于' . $sonUser->level_cc_settlement);
                        }
                    }
                    return true;
                }
            ],
            'level_dc_settlement' => [
                [
                    'level_dc_settlement'
                ],
                function ($attribute) {
                    
                    $sonUser = $this->maxLevelDcSettlement;
                    if (! empty($sonUser)) {
                        // 如果存在下级，
                        if ($sonUser->level_dc_settlement < $this->level_dc_settlement) {
                            $this->addError($attribute, '存在下级最低借记卡结算价' . $sonUser->level_dc_settlement . '设置不能大于' . $sonUser->level_dc_settlement);
                        }
                    }
                    return true;
                }
            ],
            'capping' => [
                [
                    'capping'
                ],
                function ($attribute) {
                    $sonUser = $this->maxCapping;
                    if (! empty($sonUser)) {
                        // 如果存在下级，
                        if ($sonUser->capping < $this->capping) {
                            $this->addError($attribute, '存在下级最低借记卡封顶结算价' . $sonUser->capping . '设置不能大于' . $sonUser->capping);
                        }
                    }
                    return true;
                }
            ],
            'cash_money' => [
                [
                    'cash_money'
                ],
                function ($attribute) {
                    $sonUser = $this->minCashMoney;
                    if (! empty($sonUser)) {
                        // 如果存在下级，
                        if ($sonUser->cash_money > $this->cash_money) {
                            $this->addError($attribute, '存在下级最低返现单价' . $sonUser->cash_money . '设置不能小于' . $sonUser->cash_money);
                        }
                    }
                    return true;
                }
            ],
            'min' => [
                [
                    'agent_id',
                    'user_id',
                    'agent_product_type_id',
                    'level_cc_settlement',
                    'level_dc_settlement',
                    'capping',
                    'cash_money'
                ],
                function ($attribute) {
                    $parent = $this->user->parent;
                    if (empty($parent)) {
                        return $this->addError($attribute, '上级代理不存在,发生系统错误');
                    }
                    $selfParent = static::findOne([
                        'agent_product_type_id' => $this->agent_product_type_id,
                        'user_id' => $parent->id
                    ]);
                    if (empty($selfParent)) {
                        $this->addError($attribute, '上级代理费率不存在,发生系统错误');
                        return false;
                    }
                    
                    if ($this->level_cc_settlement < $selfParent->level_cc_settlement) {
                        $this->addError($attribute, '贷记卡结算价不能少于' . $selfParent->level_cc_settlement);
                        return false;
                    }
                    if ($this->level_dc_settlement < $selfParent->level_dc_settlement) {
                        $this->addError($attribute, '借记卡结算价不能少于' . $selfParent->level_dc_settlement);
                        return false;
                    }
                    if ($this->capping < $selfParent->capping) {
                        $this->addError($attribute, '借记卡封顶结算价不能少于' . $selfParent->capping);
                        return false;
                    }
                    if ($this->cash_money > $selfParent->cash_money && $parent->parent_id != 0) {
                        $this->addError($attribute, '返现单价不能大于' . $selfParent->cash_money);
                        return false;
                    }
                    return true;
                }
            ],
            /*'max'=> [
                [
                    'level_cc_settlement',
                    'level_dc_settlement',
                ],
                function() {
                    $level_cc_settlement = $this->agentProductType->productType->level_cc_settlement;
                    $level_dc_settlement = $this->agentProductType->productType->level_dc_settlement;
                    $capping = $this->agentProductType->productType->capping;

                    if ($this->level_cc_settlement > $level_cc_settlement) {
                        $this->addError('level_cc_settlement', '贷记卡结算价不能大于' . $level_cc_settlement);
                        return false;
                    }
                    if ($this->level_dc_settlement > $level_dc_settlement) {
                        $this->addError('level_dc_settlement', '借记卡结算价不能大于' . $level_dc_settlement);
                        return false;
                    }
                    if ($this->capping > $capping) {
                        $this->addError('capping', '借记卡封顶结算价不能大于' . $capping);
                        return false;
                    }
                    return true;
                }
            ]*/
        ];
    }

    /**
     * 获得最大设置的本级贷记卡结算价
     *
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getMaxLevelCcSettlement()
    {
        $self = self::find();
        $self->andWhere([
            'user_id' => $this->sonUser,
            'agent_product_type_id' => $this->agent_product_type_id
        ]);
        $self->orderBy([
            'level_cc_settlement' => SORT_ASC
        ]);
        $self->limit(1);
        $self->select([
            'level_cc_settlement',
            'user_id'
        ]);
        $sonUser = $self->one();
        return $sonUser;
    }

    /**
     * 获得最大设置的本级贷记卡结算价
     *
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getMaxLevelDcSettlement()
    {
        $self = self::find();
        $self->andWhere([
            'user_id' => $this->sonUser,
            'agent_product_type_id' => $this->agent_product_type_id
        ]);
        $self->orderBy([
            'level_dc_settlement' => SORT_ASC
        ]);
        $self->limit(1);
        $self->select([
            'level_dc_settlement',
            'user_id'
        ]);
        $sonUser = $self->one();
        return $sonUser;
    }

    /**
     * 获得最大设置的本级贷记卡结算价
     *
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getMaxCapping()
    {
        $self = self::find();
        $self->andWhere([
            'user_id' => $this->sonUser,
            'agent_product_type_id' => $this->agent_product_type_id
        ]);
        $self->orderBy([
            'capping' => SORT_ASC
        ]);
        $self->limit(1);
        $self->select([
            'capping',
            'user_id'
        ]);
        $sonUser = $self->one();
        return $sonUser;
    }

    /**
     * 返现单价是不能低于他的子级的返现单价
     *
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getMinCashMoney()
    {
        $self = self::find();
        $self->andWhere([
            'user_id' => $this->sonUser,
            'agent_product_type_id' => $this->agent_product_type_id
        ]);
        $self->orderBy([
            'cash_money' => SORT_DESC
        ]);
        $self->limit(1);
        $self->select([
            'cash_money',
            'user_id'
        ]);
        $sonUser = $self->one();
        return $sonUser;
    }

    /**
     * 最小值设置
     *
     * @return \common\models\user\UserSettlement|NULL
     */
    public function getMin()
    {
        $parent = $this->user->parent;
        $selfParent = static::findOne([
            'agent_product_type_id' => $this->agent_product_type_id,
            'user_id' => $parent->id
        ]);
        return $selfParent;
    }

    public function getSonUser()
    {
        $son = User::find()->andWhere([
            'parent_id' => $this->user_id
        ])
            ->indexBy('id')
            ->select([
            'id'
        ])
            ->column();
        return $son;
    }

    public function attributeLabels()
    {
        return [
            'agent_id' => '商家ID',
            'user_id' => '用户ID',
            'agent_product_type_id' => '商家机具类型',
            'level_cc_settlement' => '贷记卡结算价',
            'level_dc_settlement' => '借记卡结算价',
            'capping' => '借记卡封顶结算价',
            'cash_money' => '返现单价'
        ];
    }

    public function getAgentProductType()
    {
        return $this->hasOne(AgentProductType::className(), [
            'id' => 'agent_product_type_id'
        ]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    public function register($event)
    {
        if (User::NOT_REGISTER == $event->sender->user->register) {
            $event->sender->user->register = User::REGISTER;
            $event->sender->user->register_time = time();
            $event->sender->user->save();
        }
        return true;
    }
}