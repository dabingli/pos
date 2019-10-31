<?php
namespace common\services;

use yii;
use yii\base\BaseObject;
use common\models\user\User;
use common\models\Profit;
use common\models\Order;

/**
 * 激活返现
 *
 * @author Administrator
 *        
 */
class ActivationCash extends BaseObject
{

    public $id;

    public $customerName;

    public $activeId;

    public $posCati;

    public $customerNo;

    public $activeTime;

    public $serviceLevel;

    public $serviceNo;

    public $sign;

    public $created_at;

    public function activation($userId, $agentProductTypeId, $userIds)
    {
        $son = $this->son($userId, $agentProductTypeId, $userIds);
        $my = $this->my($userId, $agentProductTypeId);
        if (empty($my['userSettlementOne'])) {
            throw new \Exception('用户' . $userId . '没有登记');
        }

        $user = User::findOne(['id'=>$userId]);

        $myUserSettlementOne = isset($my['userSettlementOne']['cash_money']) ? $my['userSettlementOne']['cash_money'] : 0;
        $sonUserSettlementOne = isset($son['userSettlementOne']['cash_money']) ? $son['userSettlementOne']['cash_money'] : 0;
        $amount = $myUserSettlementOne - $sonUserSettlementOne;
        if ($amount < 0 && $user['parent_id'] != '0') {
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
                'frozen_earnings' => User::FROZEN_EARNINGS
            ])
                ->orderBy([
                'id' => SORT_ASC
            ])
                ->one();
        }
        $load = [
            'order' => date('YmdHis') . rand(10000, 99999),
            'merchantId' => $this->customerNo,
            'merchantName' => $this->customerName,
            'user_id' => $userId,
            'agent_id' => $my['agent_id'],
            'serialNo' => $this->posCati,
            'type' => Profit::ACTIVATION_RETURN,
            'entry' => Profit::ENTRY,
            'amount_profit' => $amount
        ];
        if (! empty($frozenUser)) {
            // 如果上级有存在冻结(包括自己)，则需要将金额冻结至上级的上级
            $profit = new Profit();
            $load['unique_order'] = Profit::createUniqueOrder();
            $load['type'] = Profit::FROZEN_RETURN;
            $load['user_id'] = $frozenUser->parent_id;
            $profit->load($load, '');
            if( $profit->save() ) {
                $res = Order::addOrder($load);
                if( $res !== true ) {
                    throw new \Exception('添加订单记录失败'. current($res));
                }
            }

            Yii::$app->services->sys->log('ActivationCash/activation', '用户' . $frozenUser->parent_id . '冻结激活反佣金额' . $amount, false);
            Yii::$app->services->agent->log('ActivationCash/activation', '用户' . $frozenUser->parent_id . '冻结激活反佣金额' . $amount, false);
            
            $sql = "UPDATE " . User::tableName() . " SET activate_money=activate_money+$amount WHERE id=" . $frozenUser->parent_id;
            $db = Yii::$app->db;
            if (! $db->createCommand($sql)->query()) {
                throw new \Exception('冻结激活反佣添加失败');
            }
            $load['entry'] = Profit::NOT_ENTRY;
            $load['type'] = Profit::ACTIVATION_RETURN;
            $load['user_id'] = $userId;
            Yii::$app->services->sys->log('ActivationCash/activation', '用户' . $userId . '被上级冻结，激活佣金返回至上级佣金金额' . $amount, false);
            Yii::$app->services->agent->log('ActivationCash/activation', '用户' . $userId . '被上级冻结，激活佣金返回至上级佣金金额' . $amount, false);
        } else {
            Yii::$app->services->sys->log('ActivationCash/activation', '用户' . $userId . '激活反佣金额' . $amount, false);
            Yii::$app->services->agent->log('ActivationCash/activation', '用户' . $userId . '激活反佣金额' . $amount, false);
            $sql = "UPDATE " . User::tableName() . " SET activate_money=activate_money+$amount WHERE id=" . $userId;
            $db = Yii::$app->db;
            if (! $db->createCommand($sql)->query()) {
                throw new \Exception('激活反佣添加失败');
            }
        }
        $profit = new Profit();
        $load['unique_order'] = Profit::createUniqueOrder();
        $profit->load($load, '');
        if ($profit->save()) {
            $res = Order::addOrder($load);
            if( $res === true ) {
                return true;
            }
            throw new \Exception('添加订单记录失败'. current($res));

        } else {
            throw new \Exception('添加失败' . current($profit->getFirstErrors()));
        }
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