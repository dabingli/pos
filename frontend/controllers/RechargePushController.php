<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\library\changjie\ReceiveOrder;
use common\models\agent\AgentRechargeLog;

class RechargePushController extends Controller
{

    public $enableCsrfValidation = false;

    public $agentTable = 'agent';

    /**
     * @actionRechargeCorpPush 支付完成异步通知
     * @echo success|error
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            
            $post = $request->post();

            if (ReceiveOrder::rsaVerify($post,$post['sign']) && $post['trade_status'] == 'TRADE_SUCCESS') {

                /**
                 * 查询畅捷交易记录
                 */
                $model = new ReceiveOrder();
                $model->load([
                    'OutTradeNo' => time().mt_rand(10010, 99990),
                    'OriOutTradeNo' => $post['outer_trade_no']
                ], '');
                $http = $model->http();
                $content = $http->send()->content;
                $content = json_decode($content, true);
                // 支付成功
                if($content['OriginalRetCode'] == '000000') {

                    $agentRechargeLogModel = new AgentRechargeLog();
                    $rechargeLog = $agentRechargeLogModel->getRechargeLogByRechargeNo($post['outer_trade_no']);
                    if (isset($rechargeLog['id']) && $rechargeLog['status'] == 1) {

                        $rechargeLog->load([
                            'notify_at' => strtotime($post['notify_time']),
                            'pay_money' => round($post['trade_amount'], 2),
                            'trade_no' => $post['inner_trade_no'],
                            'pay_at' => time(),
                            'status' => 2
                        ], '');

                        if ($rechargeLog->save()) {

                            $sql = 'UPDATE ' . $this->agentTable . ' SET ';

                            // 代付金充值
                            if ($rechargeLog['type'] == 1) {

                                $sql .= " balance = balance + {$rechargeLog['money']}, ";
                                $sql .= " recharge_balance_total = recharge_balance_total + {$rechargeLog['money']} ";

                                // 短信充值
                            } elseif ($rechargeLog['type'] == 2) {

                                $sql .= " remaining_sms_number = remaining_sms_number + {$rechargeLog['sms_number']}, ";
                                $sql .= " recharge_sms_total = recharge_sms_total + {$rechargeLog['sms_number']} ";

                            }

                            $sql .= " WHERE id={$rechargeLog['agent_id']}";
                            $res = Yii::$app->db->createCommand($sql)->execute();
                            if ($res) {
                                echo 'success';
                                exit();
                            }
                        }
                    }
                }
            }
        }
        
        echo 'error';
    }
}