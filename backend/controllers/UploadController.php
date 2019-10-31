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

class UploadController extends Controller
{

    public function actionIndex()
    {
        set_time_limit(0);

        $file = '../controllers/activate.xlsx';
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

            echo " 行数：{$k} posCati: {$v['posCati']}  &nbsp;";
            $res = $this->activateInfo($v);
            var_dump($res);
            ob_flush();

            if ($k % 20 == 0) sleep(2);

            if($res !== true){
                $error_data[] = $v;
            }
        }

        if(!empty($error_data)){
            $file = '../controllers/activate.json';
            file_put_contents($file, json_encode($error_data, JSON_UNESCAPED_UNICODE), FILE_APPEND);
        }

        ob_end_flush();
    }

    public function actionOrder()
    {
        set_time_limit(0);

        $file = '../controllers/order.xlsx';
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

            echo " 行数：{$k} orderId: {$v['orderId']}  &nbsp;";
            $res = $this->orderInfo($v);
            var_dump($res);
            ob_flush();

            if ($k % 20 == 0) sleep(2);

            if($res !== true){
                $error_data[] = $v;
            }
        }

        if(!empty($error_data)){
            $file = '../controllers/order.json';
            file_put_contents($file, json_encode($error_data, JSON_UNESCAPED_UNICODE), FILE_APPEND);
        }

        ob_end_flush();
    }

    private function orderInfo($data)
    {
        $row = [
            "customerName"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['customerName']),
            "createTime"=>$data['createTime'],
            "agentLevel"=>"B",
            "isDoubleFree"=>'N',
            "posCati"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['posCati']),
            "customerNo"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['customerNo']),
            "cardType"=>$this->getCardType(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['cardType'])),
            "orderStatus"=>"SUCCESS",
            "serviceNo"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['serviceNo']),
            "sign"=>'',
            "amount"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['amount']),
            "transType"=>"PURCHASE",
            "creditRate"=>$data['creditRate'],
            "rate"=>$data['rate'],
            "agentName"=>$data['agentName'],
            "upperLimitFee"=>$data['upperLimitFee'],
            "completeTime"=>$data['completeTime'],
            "orderId"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['orderId'])
        ];

        return $this->order($row);
    }

    private function getCardType($type)
    {
        $type = trim($type);

        switch ($type) {
            case '贷记卡':
                return 'CREDIT_CARD';
            case '借记卡':
                return 'DEBIT_CARD';
            case '准贷记卡':
                return 'SEMI_CREDIT_CARD';
        }

    }

    private function activateInfo($data)
    {
        static $k = 0;
        $row = [
            "customerName"=>$data['customerName'],
            "posCati"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['posCati']),
            "customerNo"=>$data['customerNo'],
            "activeTime"=>trim($data['activeTime']),
            "activeId"=>'imp'.'500'.$k,
            "serviceNo"=>preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$data['serviceNo']),
            "serviceLevel"=>strtoupper($data['serviceLevel']),
            "sign"=>""
        ];

        ++$k;

        var_dump($this->activate($row, trim($data['userCode'])));
    }

    private function activate($data=[], $userCode)
    {
        if (ActivationLog::findOne([
            'activeId' => $data['activeId']
        ])) {
            return '激活记录已存在';
        }

        $beginTransaction = Yii::$app->db->beginTransaction();

        try {
            $model = new ActivationLog();
            $model->load($data, '');
            $model->validate();
            $model->save();

            $product = Product::findOne([
                'product_no' => $data['posCati']
            ]);
            if (empty($product)) {
                throw new \Exception( '机具不存在，或者未入库: ' . $data['posCati']);
            }

            if ($product->user_code != $userCode) {
                throw new \Exception( '机具所属的秒结平台不匹配: ' . $data['posCati']);
            }

            $product->activate_status = Product::YES;
            $product->activate_time = time();
            $product->save();

            $agent = $product->agent;
            if (empty($agent)) {
                throw new \Exception('机具所属代理商不存在: ' . $data['posCati']);
            }

            $merchantUser = new MerchantUser();
            $merchantUser->load([
                'merchantId' => $model['customerNo'],
                'merchantName' => $model['customerName'],
                'organId' => $model['serviceNo'],
                'serialNo' => $model['posCati'],
                'terminalId' => $model['activeId'],
                'agent_id' => $agent->id,
                'user_id' => $product->user->id,
                'bindingTime' => strtotime($model['activeTime']),
                'phone' => $product->user->mobile
            ], '');
            $merchantUser->save();
            // 添加累计数量
            $transactionTotal = TransactionTotal::findOne([
                'user_id' => $product->user->id,
                'agent_id' => $agent->id
            ]);

            if (is_null($transactionTotal)) {
                $transactionTotal = new TransactionTotal();
                $transactionTotal->user_id = $product->user->id;
                $transactionTotal->agent_id = $agent->id;
                $transactionTotal->num = 0;
            }

            $transactionTotal->num = $transactionTotal->num + 1;
            $transactionTotal->save();

            $beginTransaction->commit();
            return true;

        } catch (\Exception $e) {

            $beginTransaction->rollBack();
            return $e->getMessage();
        }
    }

    private function order($data=[])
    {
        if(empty($data['orderId']) || empty($data['posCati'])) return '订单号或机具编号为空：orderId:' . $data['orderId'] .'， posCati:'.$data['posCati'];

        $model = PosOrderLog::findOne(['orderId' => $data['orderId']]);

        if (!empty($model)) {
            if( Transaction::findOne(['orderNo'=>$data['orderId']]) ) return '交易记录已存在' . $data['orderId'];
        } else {
            $model = new PosOrderLog();
            $model->load($data, '');
            $model->validate();
            $model->save();
        }

        $beginTransaction = Yii::$app->db->beginTransaction();

        try {

            $product = Product::findOne([
                'product_no' => $model->posCati // $this->formData['posCati']
            ]);
            if (empty($product)) {
                throw new \Exception( '机具不存在，或者未入库 : ' . $model->posCati);
            }

            // 先根据这个机具的编号找到是谁的机具
            $agent = $product->agent;
            if (empty($agent)) {
                throw new \Exception('找不到该机具所属代理商 '. $model->posCati);
            }

            $key = $this->getRate($model['cardType']);
            $rate = $model[$key];

            if ($model['cardType'] == 'DEBIT_CARD' || $model['cardType'] == 'PREPAID_CARD') {
                // 借记卡,有封顶
                $fee = ($model['amount'] * $model['rate']) > $model['upperLimitFee'] ? $model['upperLimitFee'] : ($model['amount'] * $model['rate']);
            } else {
                $fee = ($model['amount'] * $model['creditRate']);
            }

            $transaction = new Transaction();
            $transaction->load([
                'merchantId' => $model['customerNo'],
                'merchantName' => $model['customerName'],
                'terminalId' => '',
                'bindingTime' => '',
                'orderNo' => $model['orderId'],
                'txDate' => $model['createTime'],
                'agent_id' => $agent->id,
                'user_id' => $product->user->id,
                'txTime' => $model['completeTime'],
                'txAmt' => $model['amount'],
                'regDate' => date('Y-m-d H:i:s'),
                'cardType' => $model['cardType'],
                'transType' => $model['transType'],
                'rate' => $rate,
                'amountArrives' => $model['amount'] - $fee,
                'fee' => $fee,
                'serialNo' => $model['posCati']
            ], '');
            if (!($transaction->save())) {
                throw new \Exception($transaction->getFirstErrors());
            }

            // 添加累计数量
            $transactionTotal = TransactionTotal::findOne([
                'user_id' => $product->user->id,
                'agent_id' => $agent->id
            ]);

            if (is_null($transactionTotal)) {
                $transactionTotal = new TransactionTotal();
                $transactionTotal->user_id = $product->user->id;
                $transactionTotal->agent_id = $agent->id;
                $transactionTotal->total_money = 0;
            }

            $transactionTotal->total_money = $transactionTotal->total_money + $model['amount'];
            if (!($transactionTotal->save())) {
                throw new \Exception($transactionTotal->getFirstErrors());
            }

            $beginTransaction->commit();

            // 检查满返奖励
            Yii::$app->queue->push(new ReturnRewards([
                'orderNo' => $transaction->orderNo,
            ]));

            return true;

        } catch (\Exception $e) {
            $beginTransaction->rollBack();
            return $e->getMessage();
        }
    }

    private function getRate($cardType)
    {
        $rateKey = [
            'DEBIT_CARD' => 'rate',
            'CREDIT_CARD' => 'creditRate',
            'PREPAID_CARD' => 'rate',
            'SEMI_CREDIT_CARD' => 'creditRate',
        ];

        return $rateKey[$cardType];
    }
}
