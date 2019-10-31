<?php
namespace common\services;

use yii;
use yii\base\BaseObject;
use common\models\user\User;
use common\models\Profit;
use common\models\agent\AgentProductType;
use common\models\Order;

/**
 * 分润返现
 *
 * @author Administrator
 *        
 */
class PosOrderCash extends BaseObject
{

    public $id;

    public $orderId;

    public $posCati;

    public $transType;

    public $cardType;

    public $amount;

    public $createTime;

    public $completeTime;

    public $orderStatus;

    public $customerNo;

    public $customerName;

    public $serviceNo;

    public $agentName;

    public $agentLevel;

    public $rate;

    public $creditRate;

    public $upperLimitFee;

    public $sign;

    public $created_at;
    
    public $isDoubleFree;

    public static $total = 0;

    public function profit($userId, $agentProductTypeId, $userIds)
    {
        $son = $this->son($userId, $agentProductTypeId, $userIds);
        $my = $this->my($userId, $agentProductTypeId);
        if (empty($my['userSettlementOne'])) {
            throw new \Exception('用户' . $userId . '没有登记');
        }

        if ($this->cardType == 'DEBIT_CARD' || $this->cardType == 'PREPAID_CARD') {
            $payType = 1;
            // 如果是借记卡
            $myUserSettlementOne = isset($my['userSettlementOne']['level_dc_settlement']) ? $my['userSettlementOne']['level_dc_settlement'] : 0;
            $sonUserSettlementOne = isset($son['userSettlementOne']['level_dc_settlement']) ? $son['userSettlementOne']['level_dc_settlement'] : 0;

            // 如本级借记卡结算费率大于本次交易借记卡费率 则收益为0
            if ( $myUserSettlementOne >= ($this->rate * 100) ) {
                $amount = 0;

            }else {

                // 如下级借记卡结算费率大于本次交易借记卡费率 则以本次交易费率计算
                if ($sonUserSettlementOne > ($this->rate * 100)) $sonUserSettlementOne = ($this->rate * 100);

                $isCapping = $this->isCapping($agentProductTypeId);
                if ($isCapping === true) {
                    // 借记卡有一个最高限制
                    $sonUserSettlementOne = isset($son['userSettlementOne']['capping']) ? $son['userSettlementOne']['capping'] : 0;
                    $myUserSettlementOne = isset($my['userSettlementOne']['capping']) ? $my['userSettlementOne']['capping'] : 0;

                    // 如本级借记卡结算价大于等于本次交易的借记卡封顶金额 则收益为0
                    if ($myUserSettlementOne >= $this->upperLimitFee) {
                        $amount = 0;

                    }else {

                        // 如下级借记卡结算价大于本次交易的借记卡封顶金额 则以本次交易的封顶金额计算
                        if ($sonUserSettlementOne > $this->upperLimitFee) $sonUserSettlementOne = $this->upperLimitFee;

                        if ($sonUserSettlementOne <= 0) {
                            // 如果没有子级，该级获得所有利益
                            // 原后台设置的封顶金额
//                    $getProductType = $this->getProductType($agentProductTypeId);
//                    $amount = $getProductType->capping - $myUserSettlementOne;
                            // 支付公司返回的封顶金额
                            $amount = $this->upperLimitFee - $myUserSettlementOne;

                        } else {
                            $amount = $sonUserSettlementOne - $myUserSettlementOne;
                        }
                    }

                } else {
                    if ($sonUserSettlementOne <= 0) {
                        // 如果子级没有登记，哪么所有利润全父级得
                        $getProductType = $this->getProductType($agentProductTypeId);
                        $amount = ($getProductType->level_dc_settlement - $myUserSettlementOne) / 100 * $this->amount;
                    } else {
                        $amount = ($sonUserSettlementOne - $myUserSettlementOne) / 100 * $this->amount;
                    }
                }
            }

        } elseif ($this->cardType == 'CREDIT_CARD' || $this->cardType == 'SEMI_CREDIT_CARD') {
            $payType = 2;
            $myUserSettlementOne = isset($my['userSettlementOne']['level_cc_settlement']) ? $my['userSettlementOne']['level_cc_settlement'] : 0;
            $sonUserSettlementOne = isset($son['userSettlementOne']['level_cc_settlement']) ? $son['userSettlementOne']['level_cc_settlement'] : 0;
            // 如本级贷记卡结算费率大于本次交易贷记卡费率 则收益为0
            if ( $myUserSettlementOne >= ($this->creditRate * 100) ) {
                $amount = 0;

            } else {

                // 如下级贷记卡结算费率大于本次交易贷记卡费率 则以本次交易费率计算
                if ($sonUserSettlementOne > ($this->creditRate * 100)) $sonUserSettlementOne = ($this->creditRate * 100);

                if ($sonUserSettlementOne <= 0) {
                    // 如果子级没有登记，哪么所有利润全父级得
                    // 原使用机具类型的贷记卡费率
//                    $getProductType = $this->getProductType($agentProductTypeId);
//                    $amount = ($getProductType->level_cc_settlement - $myUserSettlementOne) / 100 * $this->amount;
                    // 使用本次交易的贷记卡费率
                    $amount = ($this->creditRate - ($myUserSettlementOne / 100)) * $this->amount;

                } else {
                    $amount = ($sonUserSettlementOne - $myUserSettlementOne) / 100 * $this->amount;
                }
            }
        }
        if ($amount < 0) {
            throw new \Exception('计算错误，金额少于0');
        }
        // 判断他的上级有没有被冻结
        $parents = User::findOne([
            'id' => $userId
        ])->userLink->parents()
            ->select([
            'user_id'
        ])
            ->indexBy('user_id')
            ->column();
        if (! empty($parents)) {
            $parents[] = $userId;
            $frozenUser = User::find()->andWhere([
                'id' => $parents,
                'frozen_distributing' => User::FROZEN_DISTRIBUTING
            ])
                ->orderBy([
                'id' => SORT_ASC
            ])
                ->one();
        }

        $load = [
            'order' => $this->orderId,
            'merchantId' => $this->customerNo,
            'merchantName' => $this->customerName,
            'user_id' => $userId,
            'agent_id' => $my['agent_id'],
            'serialNo' => $this->posCati,
            'transaction_amount' => $this->amount,
            'type' => Profit::TRANSACTION_DISTRIBUTION,
            'entry' => Profit::ENTRY,
            'amount_profit' => $amount
        ];

        if (! empty($frozenUser)) {
            // 如果上级有存在冻结(包括自己)，则需要将金额冻结至上级的上级
            $profit = new Profit();
            $load['unique_order'] = Profit::createUniqueOrder();
            $load['type'] = Profit::FROZEN_DISTRIBUTION;
            $load['user_id'] = $frozenUser->parent_id;
            $profit->load($load, '');
            if( $profit->save() ) {
                $res = Order::addOrder($load, $payType);
                if( $res !== true ) {
                    throw new \Exception('添加订单记录失败'. current($res));
                }
            }

            Yii::$app->services->sys->log('PosOrderCash/profit', '用户' . $frozenUser->parent_id . '冻结反佣金额' . $amount, false);
            Yii::$app->services->agent->log('PosOrderCash/profit', '用户' . $frozenUser->parent_id . '冻结反佣金额' . $amount, false);
            
//            $sql = "UPDATE " . User::tableName() . " SET profit_money=profit_money+$amount WHERE id=" . $frozenUser->parent_id;
            $sql = $this->getProfitSql($frozenUser->parent_id, $amount);
            if (! Yii::$app->db->createCommand($sql)->query()) {
                throw new \Exception('冻结反佣添加失败');
            }
            $load['entry'] = Profit::NOT_ENTRY;
            $load['type'] = Profit::TRANSACTION_DISTRIBUTION;
            $load['user_id'] = $userId;
            Yii::$app->services->sys->log('PosOrderCash/profit', '用户' . $userId . '被上级冻结，佣金返回至上级佣金金额' . $amount, false);
            Yii::$app->services->agent->log('PosOrderCash/profit', '用户' . $userId . '被上级冻结，佣金返回至上级佣金金额' . $amount, false);
        } else {
            Yii::$app->services->sys->log('PosOrderCash/profit', '用户' . $userId . '反佣金额' . $amount, false);
            Yii::$app->services->agent->log('PosOrderCash/profit', '用户' . $userId . '反佣金额' . $amount, false);
//            $sql = "UPDATE " . User::tableName() . " SET profit_money=profit_money+$amount WHERE id=" . $userId;
            $sql = $this->getProfitSql($userId, $amount);
            if (! Yii::$app->db->createCommand($sql)->query()) {
                throw new \Exception('添加失败');
            }
        }
        $profit = new Profit();
        $load['unique_order'] = Profit::createUniqueOrder();
        $profit->load($load, '');

        if ($profit->save()) {
            $res = Order::addOrder($load, $payType);
            if( $res === true ) {
                return true;
            }
            throw new \Exception('添加订单记录失败'. current($res));

        } else {
            throw new \Exception('添加失败' . current($profit->getFirstErrors()));
        }
    }

    /**
     * @param $userId
     * @param $money
     * @return string
     */
    public function getProfitSql($userId, $money)
    {
        $sql = 'UPDATE '.User::tableName().' as a ';
        $sql .= ', (SELECT id ';
        $sql .= ", (IF(activate_money < 0, (IF((profit_money + activate_money + {$money}) < 0 , 0, (profit_money + activate_money + {$money}))), profit_money + {$money})) as profit_money ";
        $sql .= ", (IF(activate_money < 0, (IF((profit_money + activate_money + {$money}) < 0 , (profit_money + activate_money + {$money}), 0)), activate_money)) as activate_money ";
        $sql .= " FROM ".User::tableName()." WHERE id={$userId} ) as b ";
        $sql .= " SET a.profit_money = b.profit_money, a.activate_money=b.activate_money ";
        $sql .= " WHERE a.id={$userId}";

        return $sql;
    }

    /**
     * 判断是否封顶
     *
     * @param unknown $agentProductTypeId            
     * @return unknown|boolean
     */
    public function isCapping($agentProductTypeId)
    {
        // 后台设置的封顶金额
//        $productType = $this->getProductType($agentProductTypeId);
//        if ($this->amount * $productType->level_dc_settlement / 100 >= $productType->capping) {
        // 支付公司的封顶金额
        if ($this->amount * $this->rate >= $this->upperLimitFee) {
            // 如果借记卡封顶了
            return true;
        } else {
            return false;
        }
    }

    public function getProductType($agentProductTypeId)
    {
        $agentProductType = AgentProductType::findOne([
            'id' => $agentProductTypeId
        ]);
        $productType = $agentProductType->productType;
        return $productType;
    }

    /**
     * 当前自己级别的费率
     *
     * @param unknown $userId            
     * @param unknown $agentProductTypeId            
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function my($userId, $agentProductTypeId)
    {
        $my = User::find()->select([
            'parent_id',
            'id',
            'agent_id'
        ])
            ->andWhere([
            'id' => $userId
        ])
            ->with([
            'userSettlementOne' => function ($q) use ($agentProductTypeId) {
                $q->andWhere([
                    'agent_product_type_id' => $agentProductTypeId
                ]);
            }
        ])
            ->asArray()
            ->one();
        return $my;
    }

    /**
     * 子级的费率
     *
     * @param unknown $userId            
     * @param unknown $agentProductTypeId            
     */
    public function son($userId, $agentProductTypeId, $userIds)
    {
        $son = User::find()->select([
            'parent_id',
            'id',
            'agent_id'
        ])
            ->andWhere([
            'parent_id' => $userId,
            'id' => $userIds
        ])
            ->with([
            'userSettlementOne' => function ($q) use ($agentProductTypeId) {
                $q->andWhere([
                    'agent_product_type_id' => $agentProductTypeId
                ]);
            }
        ])
            ->asArray()
            ->one();
        return $son;
    }

}