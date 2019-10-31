<?php
namespace common\services\agent;

use common\models\agent\Agent;
use common\models\agent\AgentFrozenLog;
use common\models\AgentProductType;
use common\models\Order;
use common\models\product\Product;
use common\models\product\ProductType;
use Yii;
use common\services\Service;
use common\models\user\User;

class AgentFrozenLogService extends Service
{

    /**
     * @addFrozenLog 添加冻结款记录
     * @param array $userInfo
     * @param array $productInfo
     * @param int $money
     * @return bool
     */
    public function addFrozenLog($userInfo=[], $productInfo=[], $money=0)
    {

        if(empty($userInfo) || empty($productInfo) || $money <= 0){
            return false;
        }

        // 获取代理商信息
//        $agent = (new Agent()) -> findOne(['id' => $userInfo['agent_id']]);

        // 获取机具类型信息
        $agentProductType = (new AgentProductType()) -> findOne(['id'=>$productInfo['type_id']]);
        $productType = (new ProductType()) -> findOne(['id' => $agentProductType['product_type_id']]);

        // 开启事务操作
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {

            // 更新用户收益 交易分润为负数
            /*$sql = ' UPDATE '.User::tableName().' SET ';
            $sql .= " profit_money = IF(activate_money > 0 , (IF((activate_money - {$money}) >= 0 , profit_money , (profit_money + activate_money - {$money}))), profit_money - {$money}) ";
            $sql .= " , activate_money = IF(activate_money > 0 , (IF((activate_money - {$money}) >= 0, (activate_money - {$money}), 0)), activate_money) ";
            $sql .= " WHERE id = {$userInfo['id']}";*/
            // 激活返现为负数
            $sql = 'UPDATE '.User::tableName().' as a ';
            $sql .= ", (SELECT id ";
            $sql .= ", (IF((activate_money - {$money}) < 0, (IF((profit_money + activate_money - {$money}) < 0 , 0, (profit_money + activate_money - {$money}))), profit_money)) as profit_money ";
            $sql .= ", (IF((activate_money - {$money}) < 0, (IF((profit_money + activate_money - {$money}) < 0 , (profit_money + activate_money - {$money}), 0)), activate_money - {$money})) as activate_money ";
            $sql .= " FROM ".User::tableName()." WHERE id={$userInfo['id']} ) as b ";
            $sql .= " SET a.profit_money = b.profit_money, a.activate_money=b.activate_money ";
            $sql .= " WHERE a.id={$userInfo['id']}";
            $db->createCommand($sql)->execute();

            // 更新机具冻结款状态
            $sql = ' UPDATE '.Product::tableName().' SET ';
            $sql .= " frozen_status = " . Product::FROZEN;
            $sql .= " WHERE id = {$productInfo['id']} AND frozen_status = " . Product::NO_FROZEN;
            $db->createCommand($sql)->execute();

            // 添加冻结记录
            $agentFrozenLogModel = new AgentFrozenLog();
            $agentFrozenLogModel->load([
                'agent_id' => $userInfo['agent_id'],
                'user_id' => $userInfo['id'],
                'user_name' => $userInfo['user_name'],
                'user_code' => $userInfo['user_code'],
                'mobile' => $userInfo['mobile'],
                'product_id' => $productInfo['id'],
                'product_no' => $productInfo['product_no'],
                'type_id' => $productInfo['type_id'],
                'type_name' => $productType['name'],
                'model' => $productInfo['model'],
                'expire_at' => $productInfo['expire_time'],
                'frozen_money' => $money
            ], '');
            $agentFrozenLogModel -> save();

            $time = time();
            $orderNo = 'FZ'.$time.mt_rand(10000, 999999);
            $order = new Order();
            $order->load([
                'user_id' => $userInfo['id'],
                'agent_id' => $userInfo['agent_id'],
                'type' => Order::FROZEN_REWARDS,
                'amount' => $money,
                'status' => 0,
                'entry' => 0,
                'created_at' => $time,
                'updated_at' => $time,
                'order' => $orderNo,
                'unique_order' => $orderNo,
                'pay_type' => 0,
            ], '');

            $order->save();

            // 提交事务
            $transaction->commit();

            return true;

        } catch(\Exception $e) {

            //回滚事务
            $transaction->rollBack();
            Yii::warning('冻结款失败: '.$e->getMessage());
            return false;
        }

    }

}