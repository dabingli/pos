<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\pos\ActivationLog;
use common\models\product\Product;
use common\models\Transaction;
use common\models\MerchantUser;
use common\models\TransactionTotal;
use common\models\pos\PosOrderLog;
use common\models\agent\AgentProductType;
use common\components\queue\job\ReturnRewards;
use common\models\user\User;
use common\models\Profit;
use common\models\Order;

class BackController extends Controller
{

    public function actionBackProfit(){
        set_time_limit(0);

        $file = '../controllers/back.xlsx';
        $tag_data = \moonland\phpexcel\Excel::import($file, [
            'setFirstRecordAsKeys' => true,
            'setIndexSheetByName' => true,
            'getOnlySheet' => 'sheet1'
        ]);

        if(empty($tag_data) || !is_array($tag_data)) die('文件内容为空');

        ob_start();
        echo '<pre>';
        $error_data = [];
        foreach ($tag_data as $k=>$v){

            echo " 行数：{$k} user_name: {$v['user_name']}  &nbsp;";
            $res = $this->backInfo($v);
            var_dump($res);
            ob_flush();

            if ($k % 20 == 0) sleep(2);

            if($res !== true){
                $error_data[] = $v;
            }
        }

        if(!empty($error_data)){
            $file = '../controllers/back.json';
            file_put_contents($file, json_encode($error_data, JSON_UNESCAPED_UNICODE), FILE_APPEND);
        }

        ob_end_flush();
    }

    public function backInfo($data){
        $user = User::findOne(['user_code'=> $data['user_code']]);
        $transaction = Transaction::find()->where(['user_id'=>$user['id']])->orderBy('id desc')->asArray()->one();
        $db = Yii::$app->db;
        $ts = $db->beginTransaction();

        // 满足所有条件，发放满返奖励
        try{
            // 添加收益记录
            $profit = new Profit();
            $load = [
                'unique_order' => Profit::createUniqueOrder(),
                'order' => $transaction['orderNo'],
                'merchantId' => $transaction['merchantId'],
                'merchantName' => $transaction['merchantName'],
                'user_id' => $transaction['user_id'],
                'agent_id' => $transaction['agent_id'],
                'serialNo' => $transaction['serialNo'],
                'transaction_amount' => $data['amount'],
                'type' => Profit::RETURN_REWARDS,
                'entry' => Profit::ENTRY,
                'amount_profit' => $data['amount']
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


            // 增加返现收益
            $sql = "UPDATE " . User::tableName() . " SET activate_money=activate_money+{$data['amount']} WHERE id=" . $user['id'];
            if ( !($db->createCommand($sql)->query()) ) {
                throw new \Exception('add activate money fail ');
            }

            $ts->commit();
            return true;

        } catch (\Exception $e){
            var_dump($e->getMessage());die;
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
