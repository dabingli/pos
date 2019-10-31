<?php
/**
 * @满返奖励
 * @user pei
 * @date 2019-07-02 19:47
 */
namespace common\components\queue\job;

use yii;
use yii\queue\JobInterface;
use common\models\user\User;
use common\models\Transaction;
use common\models\MerchantUser;
use common\models\product\Product;
use common\models\AgentProductType;
use common\models\Profit;
use common\models\Order;

class ReturnRewards extends BaseObject implements JobInterface
{
    // 交易订单号
    public $orderNo;

    public function execute($queue)
    {
        // 检查交易记录是否存在
        $transaction = Transaction::findOne(['orderNo'=>$this->orderNo]);
        if(empty($transaction)) return $this->log('this Transaction is null');

        // 获取机具信息
        $product = Product::findOne(['product_no'=>$transaction['serialNo']]);
        if(empty($product)) return $this->log('this product is null');
        if($product['return_rewards_status'] == Product::REWARDS) return $this->log('this product already get rewards');

        // 获取机具类型满返设置
        $agentProductType = AgentProductType::findOne(['id'=>$product['type_id'], 'agent_id'=>$product['agent_id']]);
        if(empty($agentProductType)) return $this->log('this product type is null');
        if($agentProductType['return_days'] == 0) return $this->log('this product type not settings rewards');

        // 检查机具是否属于激活商户
        $merchantUser = MerchantUser::findOne(['user_id'=>$transaction['user_id'], 'serialNo'=>$transaction['serialNo']]);
        if(empty($merchantUser)) return $this->log('this product not this merchant user activate');

        // 检查是否在满返设置的交易天数内
        $returnTime = $merchantUser['bindingTime'] + ($agentProductType['return_days'] * 86400);
        if(time() > $returnTime) return $this->log('not in settings rewards days');

        // 检查是否满足交易累计金额  只累计贷记卡
        $cardType = [
            'CREDIT_CARD',
            'SEMI_CREDIT_CARD'
        ];
        $txAmt = Transaction::find()->where(['user_id'=>$transaction['user_id'], 'serialNo'=>$transaction['serialNo'], 'cardType'=>$cardType])->sum('txAmt');
        if($txAmt < $agentProductType['return_order_total_money']) return $this->log('not satisfy settings the order total money');

        // 获取该机具的用户信息
        $user = User::findOne(['id'=>$transaction['user_id']]);
        if(empty($user)) return $this->log('this product user is null');

        //开启事务
        $db = Yii::$app->db;
        $ts = $db->beginTransaction();

        // 满足所有条件，发放满返奖励
        try{
            // 添加收益记录
            $profit = new Profit();
            $load = [
                'unique_order' => Profit::createUniqueOrder(),
                'order' => $this->orderNo,
                'merchantId' => $transaction['merchantId'],
                'merchantName' => $transaction['merchantName'],
                'user_id' => $transaction['user_id'],
                'agent_id' => $transaction['agent_id'],
                'serialNo' => $transaction['serialNo'],
                'transaction_amount' => $txAmt,
                'type' => Profit::RETURN_REWARDS,
                'entry' => Profit::ENTRY,
                'amount_profit' => $agentProductType['return_rewards_money']
            ];
            $profit->load($load, '');
            if( !($profit->save()) ) {
                throw new \Exception('add profit fail :'. current($profit->getFirstErrors()));
            }

            // 添加订单记录
            $res = Order::addOrder($load);
            if( $res !== true ) {
                throw new \Exception('add order fail :'. current($res));
            }

            // 更新领取状态
            $product['return_rewards_status'] = Product::REWARDS;
            if( !($product->save()) ) {
                throw new \Exception('update product status fail :'. current($product->getFirstErrors()));
            }

            // 增加返现收益
            $sql = "UPDATE " . User::tableName() . " SET activate_money=activate_money+{$agentProductType['return_rewards_money']} WHERE id=" . $user['id'];
            if ( !($db->createCommand($sql)->query()) ) {
                throw new \Exception('add activate money fail ');
            }

            $ts->commit();
            return true;

        } catch (\Exception $e){
            $ts->rollBack();
            return $this->log( $e->getMessage() );
        }

    }

    /**
     * @param $msg
     * @return bool
     */
    public function log($msg)
    {
        Yii::warning(['orderNo'=>$this->orderNo, 'message'=>$msg]);
        return false;
    }

}