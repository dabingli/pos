<?php
namespace backend\modules\transaction\controllers;

use common\models\Transaction;
use moonland\phpexcel\Excel;
use yii;

class MerchantController extends BaseController
{

    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        //获取基信息
        $model = $this->getBaseData('post');
        $data['total'] = $model->count();
        
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $all = $model->all();
        $data['rows'] = [];
        foreach ($all as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'merchantId' => $m->merchantId,
                'real_name' => $m->user->real_name,
                'user_code' => $m->user->user_code,
                'account' => $m->user->mobile,
                'merchantName' => $m->merchantName,
                'orderNo' => $m->orderNo,
                'txDate' => $m->txDate,
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'user_id' => $m->user_id,
                'txTime' => $m->txTime,
                'txAmt' => $m->txAmt,
                'regDate' => $m->regDate,
                'transType' => $m->getType(),
                'cardType' => $m->cardType,
                'rate' => $m->rate,
                'amountArrives' => $m->amountArrives,
                'fee' => $m->fee,
                'serialNo' => $m->serialNo,
                'agent_name' => $m['agent']['name']
            ];
        }
        
        return $this->asJson($data);
    }
    /**
     * type 类型分为 post 和 get
     * return model
     */
    private function getBaseData($type='post'){
        $request = $this->request;
        $model = Transaction::find();
        $model->alias('a');
        $model->andFilterWhere([
            'like',
            'a.merchantId',
            $request->$type('merchantId')
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantName',
            $request->$type('merchantName')
        ]);
        $model->andFilterWhere([
            'like',
            'a.orderNo',
            $request->$type('orderNo')
        ]);
        $model->andFilterWhere([
            'like',
            'a.serialNo',
            $request->$type('serialNo')
        ]);
        if ($request->$type('user_code') || $request->$type('real_name')) {
            $model->joinWith([
                'user' => function ($q) use($type) {
                    $q->andFilterWhere([
                        'like',
                        'b.user_code',
                        $this->request->$type('user_code')
                    ]);
                    $q->andFilterWhere([
                        'like',
                        'b.real_name',
                        $this->request->$type('real_name')
                    ]);
                    $q->alias('b');
                }
            ]);
        } else {
            $model->with('user');
        }

        if($this->request->post('agent_name'))
        {
            $model->joinWith(['agent'=>function($q){
                $q->andFilterWhere([
                    'like',
                    'name',
                    $this->request->post('agent_name')
                ]);
            }]);
        }else{
            $model->with('agent');
        }

        if ($request->$type('created_at_start')) {
            $model->andFilterWhere([
                '>=',
                'a.txTime',
                $request->$type('created_at_start')
            ]);
        }
        if ($request->$type('created_at_end')) {
            $model->andFilterWhere([
                '<=',
                'a.txTime',
                $request->$type('created_at_end') . ' 23:59:59'
            ]);
        }

        $model->orderBy(['txTime'=>SORT_DESC]);

        return $model;
    }

//    导出
    public function actionExport()
    {
        $data = $this->export();

        $url =  Yii::$app->request->referrer;
        ob_end_clean();//清除缓冲区,避免乱码
        $file = '商户交易记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach($data as $k => $rows)
        {
            Excel::export([
                'models' => $rows,
                'fileName' => '商户交易记录',
                'columns' => [
                    [
                        'attribute' => 'orderNo',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['orderNo'];

                        }
                    ],
                    [
                        'attribute' => 'merchantId',
                        'header' => '商户编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['merchantId'];

                        }
                    ],
                    [
                        'attribute' => 'merchantName',
                        'header' => '商户名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['merchantName'];

                        }
                    ],
                    [
                        'attribute' => 'mobile',
                        'header' => '商户手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['mobile'];
                        }
                    ],
                    [
                        'attribute' => 'real_name',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['real_name'];
                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['user_code'];
                        }
                    ],
                    [
                        'attribute' => 'serialNo',
                        'header' => '机具编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['serialNo'];
                        }
                    ],
                    [
                        'attribute' => 'cardNo',
                        'header' => '银行卡号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return '';
                        }
                    ],
                    [
                        'attribute' => 'txAmt',
                        'header' => '交易金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['txAmt'];
                        }
                    ],
                    [
                        'attribute' => 'amountArrives',
                        'header' => '到账金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['amountArrives'];
                        }
                    ],
                    [
                        'attribute' => 'rate',
                        'header' => '费率',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['rate'];
                        }
                    ],
                    [
                        'attribute' => 'fee',
                        'header' => '手续费',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['fee'];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '交易时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s',$models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'transPay',
                        'header' => '付款方式',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getType($models['transType']);
                        }
                    ],
                ]
            ]);
        }

        return yii::$app->util->alert('导出成功',$url);
        exit;
    }

    protected function export()
    {
        $model = $this->getBaseData('get');

        $count=$model->count();
        $limit=self::LIMIT;
        $model->asArray();
        for($i=0;$i<=$count;){
            $model->limit($limit)->offset($i);
            $i=$i+$limit;
            yield $model->all();
        }
    }

    public function getType($type)
    {
        switch($type){
            case 1;
                return '附近商圈';
                break;
             case 2;
                return '闪付';
                break;
              case 3;
                return '云闪付';
                break;
            case 4;
               return '传统POS闪付';
               break;
            case 6;
                return '押金交易';
                break;
             case 7;
                return '无卡交易';
                break;
        }
    }

    public function actionView()
    {
        $id = $this->request->get('id');
        if(empty($id)){
            return '';
        }

        $data = [];

        $transaction = Transaction::findOne(['id'=>$id]);

        $data['html'] = $this->renderPartial('view', ['transaction'=>$transaction]);

        return $this->asJson($data);

    }
}