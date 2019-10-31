<?php
namespace common\models;

use common\models\agent\Agent;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\user\User;
use common\models\app\AppMessage;
use common\models\app\UserMessage;
use common\services\common\AppMessage as AppMessageService;

class Profit extends ActiveRecord
{

    const ENTRY = 1;

    const NOT_ENTRY = 2;

    const ACTIVATION_RETURN = 1;

    const FROZEN_RETURN = 2;

    const TRANSACTION_DISTRIBUTION = 3;

    const FROZEN_DISTRIBUTION = 4;

    //满返奖励
    const RETURN_REWARDS = 7;

    // 大于等于该金额的收益才发送app通知
    const MIN_PROFIT_MONEY = 0.01;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->on(self::EVENT_AFTER_INSERT, [$this, 'addAppMessage']);
    }

    public static function tableName()
    {
        return '{{%profit}}';
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

    public static function typeLabels()
    {
        return [
            self::ACTIVATION_RETURN => '激活返现',
            self::FROZEN_RETURN => '冻结返现',
            self::TRANSACTION_DISTRIBUTION => '交易分润',
            self::FROZEN_DISTRIBUTION => '冻结分润',
            self::RETURN_REWARDS => '满返奖励'
        ];
    }

    public static function entryLabels()
    {
        return [
            self::ENTRY => '已入账',
            self::NOT_ENTRY => '未入账'
        ];
    }

    public function getType()
    {
        $typeLabels = self::typeLabels();
        return isset($typeLabels[$this->type]) ? $typeLabels[$this->type] : '';
    }

    public function getEntry()
    {
        $entryLabels = self::entryLabels();
        return isset($entryLabels[$this->entry]) ? $entryLabels[$this->entry] : '';
    }

    public function rules()
    {
        return [
            [
                'type',
                'in',
                'range' => array_keys(self::typeLabels())
            ],
            [
                'entry',
                'in',
                'range' => array_keys(self::entryLabels())
            ],
            [
                [
                    'unique_order',
                    'merchantId',
                    'merchantName',
                    'user_id',
                    'agent_id',
                    'serialNo',
                    'order',
                    'type',
                    'entry',
                    'amount_profit'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'transaction_amount',
                    'amount_profit'
                ],
                'double'
            ],
            [
                [
                    'user_id',
                    'agent_id',
                    'transaction_amount',
                    'type',
                    'entry',
//                    'amount_profit'
                ],
                'compare',
                'compareValue' => 0,
                'operator' => '>'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'agent_id' => '商家ID',
            'user_id' => '用户ID',
            'order' => '订单号',
            'merchantId' => '商家ID',
            'merchantName' => '商家用户名',
            'serialNo' => '机具编号',
            'transaction_amount' => '交易金额',
            'type' => '类型',
            'entry' => '是否已入帐',
            'amount_profit' => '收益金额'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'id' => 'agent_id'
        ]);
    }

    public function addAppMessage($event)
    {
        if( self::MIN_PROFIT_MONEY > $event->sender->amount_profit ) return false;

        $user = User::findOne(['id'=>$event->sender->user_id]);

        if( $event->sender->type == self::ACTIVATION_RETURN
            || $event->sender->type == self::FROZEN_RETURN )
        {
            $type = AppMessage::ACTIVATE;

        }else{

            $type = AppMessage::PROFIT;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {

            $model = new AppMessage();
            $model->load([
                'title' => $this->getType(),
                'content' => '【开店宝助手】 您好！您收到一笔（' . $this->getType() . '） ' . Order::formatProfit($event->sender->amount_profit) . ' 元，请注意查收！',
                'type' => $type,
                'receiver_name' => AppMessage::NOT_ALL,
                'user_code' => $user['user_code']
            ], '');

            $model->save();

            $userMessage = new UserMessage();
            $userMessage->load([
                'app_message_id' => $model->id,
                'user_id' => $user['id'],
                'type' => $type
            ], '');
            $userMessage->save();

            if(!empty($user['client_id'])){
                (new AppMessageService($model->id))->sendSingleByClientId($user['client_id']);
            }

            $transaction->commit();

        } catch(\Exception $e) {

            $transaction->rollBack();
        }
    }

    /**
     * @createUniqueOrder 生成唯一订单号
     */
    public static function createUniqueOrder()
    {
        return 'PF' . date('YmdHis') . mt_rand(1000, 9999) . mt_rand(1000, 9999);
    }
}