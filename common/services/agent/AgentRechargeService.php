<?php
namespace common\services\agent;

use Yii;
use common\services\Service;
use common\models\agent\Agent;
use common\models\agent\AgentRechargeLog;

class AgentRechargeService extends Service
{
    public $smsFee = 0.5;

    /**
     * @ 创建代理商充值记录
     * @param string $agentId
     * @param int $type
     * @param int $money
     * @return bool|string
     */
    public function addAgentRechargeLog($agentId='', $type=1, $money=0)
    {
        if(empty($agentId) || $money <= 0){
            return false;
        }

        $title = '代理商充值';

        $agentModel = new Agent();
        $agent = $agentModel->findOne(['id'=>$agentId]);

        if(!isset($agent['id'])){
            return false;
        }

        $agentRechargeLogModel = new AgentRechargeLog();
        $rechargeNo = $agentRechargeLogModel->createRechargeNo(); //生成充值单号
        $smsNumber = $this->getRechargeSmsNumber($money);
        $authNumber = $this->getRechargeRealNameAuthNumber($money);

        $agentRechargeLogModel->load([

            'agent_id' => $agent['id'],
            'recharge_no' => $rechargeNo,
            'app_id' => Yii::$app->params['changjie']['PartnerId'],
            'type' => $type,
            'money' => $money,
            'real_name_auth_number' => $type==AgentRechargeLog::REAL_NAME ? $authNumber : 0,
            'old_real_name_auth_number' => $agent['remaining_real_name_auth_number'],
            'new_real_name_auth_number' => $type==AgentRechargeLog::REAL_NAME ? $agent['remaining_real_name_auth_number'] + $authNumber : $agent['remaining_real_name_auth_number'],
            'sms_number' => $type==AgentRechargeLog::SMS ? $smsNumber : 0,
            'old_sms_number' => $agent['remaining_sms_number'],
            'new_sms_number' => $type==AgentRechargeLog::SMS ? $agent['remaining_sms_number'] + $smsNumber : $agent['remaining_sms_number'],
            'old_money' => $agent['balance'],
            'new_money' => $type==AgentRechargeLog::PAYMENT ? $agent['balance'] + $money : $agent['balance'],
            'title' => $title,
            'status' => AgentRechargeLog::WAIT_PAY

        ], '');

        if($agentRechargeLogModel->save()){
            return $rechargeNo;
        }

        return false;
    }

    /**
     * @getRechargeSmsNumber 获取充值短信数量
     * @param $money
     * @return int
     */
    public function getRechargeSmsNumber($money=0)
    {
        $unitPrice = Yii::$app->debris->config('sms_unit_price') ? Yii::$app->debris->config('sms_unit_price') : 0.06;

        return (int)($money / $unitPrice);
    }

    /**
     * @getRechargeSmsNumber 获取充值实名认证数量
     * @param $money
     * @return int
     */
    public function getRechargeRealNameAuthNumber($money=0)
    {
        $unitPrice = Yii::$app->debris->config('real_name_auth_unit_price') ? Yii::$app->debris->config('real_name_auth_unit_price') : 0.2;

        return (int)($money / $unitPrice);
    }

    /**
     * @paySuccess 支付成功
     * @param $recharge_no
     * @return bool
     */
    public function paySuccess($recharge_no)
    {
        $agentRechargeLogModel = new AgentRechargeLog();
        $rechargeLog = $agentRechargeLogModel->getRechargeLogByRechargeNo($recharge_no);
        if (!isset($rechargeLog['id']) || $rechargeLog['status'] != AgentRechargeLog::WAIT_PAY) {
            return false;
        }

        //开启事务操作
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {

            // 更新充值记录状态
            $data = [
                'notify_at' => time(),
                'pay_money' => $rechargeLog['money'],
                'real_money' => $rechargeLog['money'],
                'pay_at' => time(),
                'status' => AgentRechargeLog::SUCCESS,
                'audit_name' => Yii::$app->user->identity->username
            ];

            $db->createCommand()->update(AgentRechargeLog::tableName(), $data, 'id = '.$rechargeLog['id'])->execute();


            // 更新代理商信息
            $sql = 'UPDATE ' . Agent::tableName() . ' SET ';
            // 代付金充值
            if ($rechargeLog['type'] == AgentRechargeLog::PAYMENT) {

                $sql .= " balance = balance + {$rechargeLog['money']}, ";
                $sql .= " recharge_balance_total = recharge_balance_total + {$rechargeLog['money']} ";

                // 短信充值
            } elseif ($rechargeLog['type'] == AgentRechargeLog::SMS) {

                $sql .= " remaining_sms_number = remaining_sms_number + {$rechargeLog['sms_number']}, ";
                $sql .= " recharge_sms_total = recharge_sms_total + {$rechargeLog['sms_number']} ";

            }elseif ($rechargeLog['type'] == AgentRechargeLog::REAL_NAME) {

                $sql .= " remaining_real_name_auth_number = remaining_real_name_auth_number + {$rechargeLog['real_name_auth_number']}, ";
                $sql .= " recharge_real_name_auth_total = recharge_real_name_auth_total + {$rechargeLog['real_name_auth_number']} ";

            }else{
                throw new \Exception('充值类型不存在');
            }
            $sql .= " WHERE id={$rechargeLog['agent_id']}";
            $db->createCommand($sql)->execute();

            // 提交事务
            $transaction->commit();

            return true;

        } catch(\Exception $e) {

            //回滚事务
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @payClose 关闭订单
     * @param $recharge_no
     * @return bool
     */
    public function payClose($recharge_no)
    {
        $agentRechargeLogModel = new AgentRechargeLog();
        $rechargeLog = $agentRechargeLogModel->getRechargeLogByRechargeNo($recharge_no);
        if (!isset($rechargeLog['id']) || $rechargeLog['status'] != AgentRechargeLog::WAIT_PAY) {
            return false;
        }

        // 更新充值记录状态
        $data = [
            'close_at' => time(),
            'status' => AgentRechargeLog::CLOSE
        ];

        return Yii::$app->db->createCommand()->update(AgentRechargeLog::tableName(), $data, 'id = '.$rechargeLog['id'])->execute();

    }

    /**
     * @deleteRechargeLog 删除未处理的订单
     * @param array $id
     * @param bool $agentId
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function deleteRechargeLog($id=[], $agentId=false)
    {
        if(empty($id) || !is_array($id)){
            return false;
        }

        $ids = implode(',', $id);

        // 删除未处理的订单
        $sql = 'UPDATE ' . AgentRechargeLog::tableName() . ' SET ';
        $sql .= ' is_deleted = ' . AgentRechargeLog::DELETED;
        $sql .= ' , deleted_at = ' . time();
        $sql .= " WHERE id in ({$ids}) ";
        $sql .= " AND status = " . AgentRechargeLog::WAIT_PAY;

        if($agentId){
            $sql .= " AND agent_id = {$agentId}";
        }

        return Yii::$app->db->createCommand($sql)->execute();
    }

}