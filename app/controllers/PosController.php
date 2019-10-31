<?php
namespace app\controllers;

use yii;
use common\models\pos\ActivationLog;
use common\models\product\Product;
use common\models\agent\AgentProductType;
use common\models\user\User;
use common\services\ActivationCash;
use common\models\Profit;
use common\models\pos\PosOrderLog;
use common\services\PosOrderCash;
use common\models\Transaction;
use common\models\MerchantUser;
use common\models\TransactionTotal;
use common\components\queue\job\ReturnRewards;

class PosController extends \yii\web\Controller
{

    public $formData;

    protected $fastcgiFinishRequest;

    public function beforeAction($action)
    {
        Yii::$app->log->targets[0]->logFile = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'pos' . date('Ymd') . '.log';
        $input = file_get_contents('php://input');
        file_put_contents(Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "post_log" . date('Y-m-d') . ".log", date('Y-m-d H:i:s') . var_export($input, true) . PHP_EOL, FILE_APPEND);
        $encryptionData = json_decode($input, true);
        $this->formData = $encryptionData;
        if (empty($this->formData)) {
            echo json_encode([
                'code' => 200,
                'msg' => '请求错误'
            ]);
            return false;
        }
        
        $sign = isset($encryptionData['sign']) ? $encryptionData['sign'] : '';
        unset($encryptionData['sign']);
        if (! ($sign == $this->getEncryptionData($encryptionData) && ! empty($sign))) {
            echo json_encode([
                'code' => 200,
                'msg' => '签名失败'
            ]);
            return false;
        }
        
        if (YII_ENV_PROD) {
            $this->fastcgiFinishRequest();
        } else {
            echo json_encode([
                'code' => '00',
                'msg' => 'success'
            ]);
        }
        return parent::beforeAction($action);
    }

    public function fastcgiFinishRequest()
    {
        $this->fastcgiFinishRequest = true;
        if (function_exists("fastcgi_finish_request")) {
            // 如果是nginx服务器php-fpm则直接fastcgi_finish_request
            echo json_encode([
                'code' => '00',
                'msg' => 'success'
            ]);
            fastcgi_finish_request();
        } else {
            // 如果是apache服务器
            ignore_user_abort(true);
            set_time_limit(0);
            ob_start();
            echo json_encode([
                'code' => '00',
                'msg' => 'success'
            ]);
            header('Connection: close');
            header('Content-Length: ' . ob_get_length());
            ob_end_flush();
            ob_flush();
            flush();
        }
    }

    /**
     * 对数数组签名
     *
     * @param unknown $data            
     * @return string
     */
    public function getEncryptionData($data)
    {
        ksort($data);
        $str = "";
        foreach ($data as $m => $n) {
            $str .= "$m=$n&";
        }
        // $str = rtrim($str, "&");
        $str .= 'key=' . Yii::$app->params['pos']['notifKey'];
        return strtoupper(md5($str));
    }

    /**
     * 点pos激活的时候调用我们的接口地址
     */
    public function actionIndex()
    {
        if (ActivationLog::findOne([
            'activeId' => $this->formData['activeId']
        ])) {
            return;
            die();
        }
        $model = new ActivationLog();
        $model->load($this->formData, '');
        $model->validate();
        try {
            $model->save();
        } catch (\Exception $e) {
            return;
            die();
        }
        
        $product = Product::findOne([
            'product_no' => $this->formData['posCati']
        ]);
        if (empty($product)) {
            Yii::warning('机具不存在或者未入库, 编号:' . $this->formData['posCati']);
            return;
            die();
        }
        $product->activate_status = Product::YES;
        $product->activate_time = time();
        $product->save();

        $agentProductType = AgentProductType::findOne([
            'id' => $product->type_id
        ]);
        if (empty($product)) {
            Yii::warning('机具类型不存在，编号：' . $this->formData['posCati']);
            return;
            die();
        }
        
        $agent = $product->agent;
        if (empty($agent)) {
            Yii::warning('找不到机具所属代理商，编号：' . $this->formData['posCati']);
            return;
            die();
        }
        Yii::$app->params['agentModel'] = $agent;
        
        $userIds = $product->productUser->userLink->parents()
            ->select([
            'user_id'
        ])
            ->indexBy('user_id')
            ->orderBy([
            'depth' => SORT_ASC
        ])
            ->column();
        $userIds = $userIds + [
            $product->productUser->id => $product->productUser->id
        ];
        $activationCash = new ActivationCash($model->toArray());
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            foreach ($userIds as $val) {
                $activationCash->activation($val, $agentProductType->id, $userIds);
            }
            // 添加交易记录
            $merchantUser = new MerchantUser();
            $merchantUser->load([
                'merchantId' => $model['customerNo'],
                'merchantName' => $model['customerName'],
                'organId' => $model['serviceNo'],
                'serialNo' => $model['posCati'],
                'terminalId' => $model['activeId'],
                'agent_id' => $agent->id,
                'user_id' => $product->productUser->id,
                'bindingTime' => strtotime($model['activeTime']),
                'phone' => $product->productUser->mobile
            ], '');
            if(!$merchantUser->save()){
                throw new \Exception('添加失败' . current($merchantUser->getFirstErrors()));
            }
            // 添加累计数量
            $transactionTotal = TransactionTotal::findOne([
                'user_id' => $product->productUser->id,
                'agent_id' => $agent->id
            ]);

            if (is_null($transactionTotal)) {
                $transactionTotal = new TransactionTotal();
                $transactionTotal->user_id = $product->productUser->id;
                $transactionTotal->agent_id = $agent->id;
                $transactionTotal->num = 0;
            }

            $transactionTotal->num = $transactionTotal->num + 1;
            if(!$transactionTotal->save()){
                throw new \Exception('添加失败' . current($transactionTotal->getFirstErrors()));
            }
            $transaction->commit();
            return;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning([
                'number' => $this->formData['posCati'],
                'message' => $e->getMessage()
            ]);
            Yii::$app->services->sys->log('ActivationCash/activation', $e->getMessage(), false);
            Yii::$app->services->agent->log('ActivationCash/activation', $e->getMessage(), false);
        }

    }

    /**
     * 点POS刷卡的时候接我们的接口
     */
    public function actionOrder()
    {
        if (empty($this->formData['orderId']) || empty($this->formData['posCati'])) {
            return;
            die();
        }
        if (PosOrderLog::findOne([
            'orderId' => $this->formData['orderId']
        ])) {
            return;
            die();
        }
        
        $model = new PosOrderLog();
        $model->load($this->formData, '');
        $model->validate();
        $model->save();
        if ($model->orderStatus !== 'SUCCESS') {
            // 如果订单不是成功状态，直接返回
            return;
            die();
        }
        
        $product = Product::findOne([
            'product_no' => $model->posCati
        ]);
        if (empty($product)) {
            Yii::warning('机具不存在或者未入库, 编号:' . $this->formData['posCati']);
            return;
            die();
        }
        if($product->frost_status == Product::FROST_START){
            Yii::warning('机具已冻结不参加分润, 编号:' . $this->formData['posCati']);
            return;
            die();
        }
        
        $agentProductType = AgentProductType::findOne([
            'id' => $product->type_id
        ]);
        if (empty($product)) {
            Yii::warning('机具类型不存在，编号:' . $this->formData['posCati']);
            return;
            die();
        }
        
        // 检查是否是激活交易记录
        if ($agentProductType->productType->activation_money == $this->formData['amount']) {
            $posOrderlog = PosOrderLog::find();
            $posOrderlog->andWhere([
                'posCati' => $this->formData['posCati']
            ]);
            $posOrderlog->andWhere([
                'amount' => $this->formData['amount']
            ]);
            $posOrderlog->andWhere([
                '!=',
                'orderId',
                $this->formData['orderId']
            ]);
            $log = $posOrderlog->one();
            if ($log === null) {
                Yii::warning('激活机具的交易记录，编号：' . $this->formData['posCati']);
                return;
                die();
            }
        }
        
        // 先根据这个机具的编号找到是谁的机具
        $agent = $product->agent;
        if (empty($agent)) {
            Yii::warning('找不到机具所属代理商，编号：' . $this->formData['posCati']);
            return;
            die();
        }
        Yii::$app->params['agentModel'] = $agent;
        
        $userIds = $product->productUser->userLink->parents()
            ->select([
            'user_id'
        ])
            ->indexBy('user_id')
            ->orderBy([
            'depth' => SORT_ASC
        ])
            ->column();
        $userIds = $userIds + [
            $product->productUser->id => $product->productUser->id
        ];
        
        $posOrderCash = new PosOrderCash($model->toArray());
        foreach ($userIds as $val) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $posOrderCash->profit($val, $agentProductType->id, $userIds);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::warning([
                    'number' => $this->formData['posCati'],
                    'message' => $e->getMessage()
                ]);
                Yii::$app->services->sys->log('PosOrderCash/profit', $e->getMessage(), false);
                Yii::$app->services->agent->log('PosOrderCash/profit', $e->getMessage(), false);
            }
        }
        
        if ($model['cardType'] == 'DEBIT_CARD' || $model['cardType'] == 'PREPAID_CARD') {
            // 借记卡,有封顶
            $fee = ($model['amount'] * $model['rate']) > $model['upperLimitFee'] ? $model['upperLimitFee'] : ($model['amount'] * $model['rate']);
        } else {
            $fee = ($model['amount'] * $model['creditRate']);
        }
        
        $key = $this->getRate($model['cardType']);
        $rate = $model[$key];
        
        $transaction = new Transaction();
        $transaction->load([
            'merchantId' => $model['customerNo'],
            'merchantName' => $model['customerName'],
            'terminalId' => '',
            'bindingTime' => '',
            'orderNo' => $model['orderId'],
            'txDate' => $model['createTime'],
            'agent_id' => $agent->id,
            'user_id' => $product->productUser->id,
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
        $transaction->save();
        
        // 添加累计数量
        $transactionTotal = TransactionTotal::findOne([
            'user_id' => $product->productUser->id,
            'agent_id' => $agent->id
        ]);
        
        if (is_null($transactionTotal)) {
            $transactionTotal = new TransactionTotal();
            $transactionTotal->user_id = $product->productUser->id;
            $transactionTotal->agent_id = $agent->id;
        }
        
        $transactionTotal->total_money = $transactionTotal->total_money + $model['amount'];
        $transactionTotal->save();
        
        // 检查满返奖励
        Yii::$app->queue->push(new ReturnRewards([
            'orderNo' => $transaction->orderNo
        ]));
        return;
    }

    protected function getRate($cardType)
    {
        $rateKey = [
            'DEBIT_CARD' => 'rate',
            'CREDIT_CARD' => 'creditRate',
            'PREPAID_CARD' => 'rate',
            'SEMI_CREDIT_CARD' => 'creditRate'
        ];
        
        return $rateKey[$cardType];
    }

    public function afterAction($action, $result)
    {
        parent::afterAction($action, $result);
        if ($this->fastcgiFinishRequest) {
            // 如果使用了fastcgiFinishRequest就die
            die();
        }
    }
}