<?php
namespace common\models\product;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\user\User;
use common\models\agent\Agent;
use common\models\Transaction;
use common\models\agent\AgentProductType;
use common\models\MerchantUser;
use common\models\user\UserSettlement;

class Product extends ActiveRecord
{

    // 激活状态
    const YES = 1;

    const NO = 2;

    // 机型状态
    const IN_STORE = 1;

    const SEND = 2;

    const REFUND = 3;

    const NO_SEND = 4;

    // 冻结状态 2019-7-26暂时不知道哪里用到
    const FROZEN = 1;

    const NO_FROZEN = 0;

    //冻结状态 2019-7-26 frost
    const FROST_START = 2;//冻结

    const FROST_STOP = 1; //取消冻结
    // 满返奖励状态
    const REWARDS = 1;

    const NO_REWARDS = 0;

//    机具类型名称
    public $type;

    static public function ActivateStatusLabels()
    {
        return [
            self::YES => '已激活',
            self::NO => '未激活'
        ];
    }

    static public function StatusLabels()
    {
        return [
            self::IN_STORE => '在库',
            self::SEND => '已下发',
            self::REFUND => '已退货',
            self::NO_SEND => '未下发'
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'product_no' => '机具编号',
            'type_id' => '机具类型',
            'store_time' => '入库日期',
            'expire_time' => '到期日期',
            'model' => '机具型号',
            'user_code' => '代理商编号',
            'user_name' => '代理商',
            'status' => '状态',
            'activate_time' => '激活时间',
            'send_time' => '下发时间',
            'refund_time' => '退货时间',
            'activate_status' => '激活状态',
            'return_rewards_status' => '满返奖励领取状态',
            'back_time' => '回拨时间',
            'mobile' => '手机号',
            'agent_id' => '代理商id'
        ];
    }

    public function rules(){
        return [
            [
                [
                    'product_no',
                    'type_id',
                    'store_time',
                    'expire_time',
                    'model',
                    'user_code',
                    'user_name',
                    'status',
                    'user_id',
                    'activate_time',
                    'send_time',
                    'refund_time',
                    'activate_status',
                    'frost_status',
                    'return_rewards_status',
                    'back_time',
                    'mobile',
                    'agent_id'
                ],
                'safe'
            ],
            [
                'frost_status',
                'default',
                'value' => self::FROST_STOP
            ],
            'product_no' => [
                [
                    'product_no'
                ],
                function(){
//                    var_dump($this->type_id);die;
                    $userSettlement = UserSettlement::findOne(['user_id'=>$this->user_id,'agent_id'=>$this->agent_id, 'agent_product_type_id'=>$this->type_id]);
                    if(empty($userSettlement))
                    {
                        $this->addError('product_no', '该代理商还没登记该机具类型的结算价');
                        return false;
                    }
                    return true;
                }
            ],
            [
                [
                    'product_no'
                ],
                'unique',
                'message' => '{attribute}唯一的'
            ]
        ];
    }

    public function getFrostStatusLables(){
        return [
            self::FROST_START => '冻结',
            self::FROST_STOP => '未冻结'
        ];
    }

    public function getFrostStatus(){
        $frostStatusLables = self::getFrostStatusLables();
        return isset($frostStatusLables[$this->frost_status]) ? $frostStatusLables[$this->frost_status] : '';
    }

    public function getStatus()
    {
        $statusLabels = self::statusLabels();
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '';
    }

    public function getActivateStatus()
    {
        $statusLabels = self::ActivateStatusLabels();
        return isset($statusLabels[$this->activate_status]) ? $statusLabels[$this->activate_status] : '';
    }

    public function getAgentProductType()
    {
        return $this->hasOne(AgentProductType::className(), [
            'id' => 'type_id'
        ]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'user_code' => 'user_code'
        ]);
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), [
            'id' => 'agent_id'
        ]);
    }

    public function getMerchantUser()
    {
        return $this->hasOne(MerchantUser::className(),[
           'serialNo' => 'product_no'
        ]);
    }

    public function getProductUser()
    {
        return $this->hasOne(User::className(),[
            'id' => 'user_id'
        ]);
    }

    /**
     * @param $productNos
     * @param $nums
     * @return bool true代表存在订单
     */
    public static function existProductOrder($productNos, $nums){

        $nos = self::getProductNos($productNos, $nums);

        return Transaction::existProductNo($nos);
    }

    /**
     * @param array $productNos
     * @param array $nums
     * @return array|bool
     */
    public static function getProductNos($productNos=[], $nums=[]){

        $nos = [];
        foreach ($productNos as $k=>$v) {
            $no = self::expProductNo($v, $nums[$k]);
            $nos = array_merge($nos, $no);
        }

        if(empty($nos)) return false;

        return $nos;
    }

    /**
     * @param int $startNo
     * @param int $length
     * @return array
     */
    public static function expProductNo($startNo=0, $length=0)
    {
        $allNo = [$startNo];
        for( $i=1; $i<$length; ++$i){

            $allNo[] = self::singleProductNo($startNo, $i);
        }

        return $allNo;
    }

    /**
     * @singleProductNo 生成机具编号 在$startNo开始编号加上$num
     * @param int $startNo
     * @param int $num
     * @return string
     */
    public static function singleProductNo($startNo=0, $num=0)
    {
        $toNo = (int)$startNo + $num;
        $n = strlen($startNo) - strlen($toNo);

        return $n > 0 ? str_repeat('0', $n).(string)$toNo : (string)$toNo;
    }
}