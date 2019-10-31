<?php
namespace frontend\modules\order\controllers;

use moonland\phpexcel\Excel;
use yii;
use common\helpers\FormHelper;
use common\models\Profit;

class IndexController extends \frontend\controllers\MController
{

    const LIMIT = 80000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = Profit::find();
        $model->alias('a');
        $model->andWhere([
            'not in',
            'user_id',
            $this->agentAppUser->id
        ]);
        $model->andFilterWhere([
            'like',
            'a.order',
            $this->request->post('profit_order')
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantId',
            $this->request->post('merchantId')
        ]);
        if ($this->request->post('user_code') || $this->request->post('real_name')) {
            $model->joinWith([
                'user' => function ($q) {
                    $q->andFilterWhere([
                        'like',
                        'b.user_code',
                        $this->request->post('user_code')
                    ]);
                    $q->andFilterWhere([
                        'like',
                        'b.real_name',
                        $this->request->post('real_name')
                    ]);
                    $q->alias('b');
                }
            ]);
        } else {
            $model->with('user');
        }
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'like',
            'a.serialNo',
            $this->request->post('serialNo')
        ]);
        $model->andFilterWhere([
            'a.type' => $this->request->post('type')
        ]);
        $model->andFilterWhere([
            'a.entry' => $this->request->post('entry')
        ]);
        if ($this->request->post('created_start')) {
            $model->andFilterWhere([
                '>=',
                'a.created_at',
                strtotime($this->request->post('created_start'))
            ]);
        }
        if ($this->request->post('created_end')) {
            $model->andFilterWhere([
                '<=',
                'a.created_at',
                strtotime($this->request->post('created_end')) + 3600 * 24 - 1
            ]);
        }
        $data['total'] = $model->count();
        $model->orderBy(['created_at'=>SORT_DESC]);
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $all = $model->all();
        $data['rows'] = [];
        foreach ($all as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'order' => $m->order,
                'real_name' => $m->user->real_name,
                'user_code' => $m->user->user_code,
                'merchantId' => $m->merchantId,
                'merchantName' => $m->merchantName,
                'user_id' => $m->user_id,
                'transaction_amount' => $m->transaction_amount,
                'type' => $m->getType(),
                'entry' => $m->getEntry(),
                'amount_profit' => $m->amount_profit,
                'serialNo' => $m->serialNo,
                'created_at' => date('Y-m-d H:i:s', $m->created_at)
            ];
        }
        
        return $this->asJson($data);
    }

//    导出
    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);

        $type = Profit::typeLabels();
        $url =  Yii::$app->request->referrer;
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        ob_end_clean();//清除缓冲区,避免乱码
        $file = '收益记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach($data as $k => $rows)
        {
            Excel::export([
                'models' => $rows,
                'fileName' => '收益记录',
                'columns' => [
                    [
                        'attribute' => 'id',
                        'header' => 'ID',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['id'];

                        }
                    ],
                    [
                        'attribute' => 'order',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['order'];

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
                            return "\t" . $models['serialNo'];
                        }
                    ],
                    [
                        'attribute' => 'transaction_amount',
                        'header' => '交易金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['transaction_amount'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '收益类型',
                        'format' => 'text',
                        'value' => function ($models)use($type) {
                            return $type[$models['type']];
                        }
                    ],
                    [
                        'attribute' => 'amount_profit',
                        'header' => '收益金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['amount_profit'];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '收益时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s',$models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'entry',
                        'header' => '是否入账',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['entry'] == Profit::ENTRY ? '已入账' : '未入账';
                        }
                    ],
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功',$url);
        exit;
    }

    public function export($get)
    {
//        var_dump($this->agentAppUser->id);die;
        $model = Profit::find();
        $model->alias('a');
        $model->andWhere([
            'not in',
            'user_id',
            $this->agentAppUser->id
        ]);
        $model->andFilterWhere([
            'like',
            'a.order',
            $get['profit_order']
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantId',
            $get['merchantId']
        ]);
        if ($get['user_code'] | $get['real_name']) {
            $model->joinWith([
                'user' => function ($q) use($get) {
                    $q->andFilterWhere([
                        'like',
                        'b.user_code',
                        $get['user_code']
                    ]);
                    $q->andFilterWhere([
                        'like',
                        'b.real_name',
                        $get['real_name']
                    ]);
                    $q->alias('b');
                }
            ]);
        } else {
            $model->with('user');
        }
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'like',
            'a.serialNo',
            $get['serialNo']
        ]);
        $model->andFilterWhere([
            'a.type' => $get['type']
        ]);
        $model->andFilterWhere([
            'a.entry' => $get['entry']
        ]);
        if ($get['created_start']) {
            $model->andFilterWhere([
                '>=',
                'a.created_at',
                strtotime($get['created_start'])
            ]);
        }
        if ($get['created_end']) {
            $model->andFilterWhere([
                '<=',
                'a.created_at',
                strtotime($get['created_end']) + 3600 * 24 - 1
            ]);
        }

        $count=$model->count();
        $limit=self::LIMIT;
        $model->asArray();
        for($i=0;$i<=$count;){
            $model->limit($limit)->offset($i);
            $i=$i+$limit;
            yield $model->all();
        }
    }

    public function actionView()
    {
        $id = $this->request->get('id');
        if(empty($id)){
            return '';
        }

        $data = [];

        $detail = Profit::findOne([
            'id'=>$id,
            'agent_id' => $this->agentId]
        );

        if(empty($detail)){
            return null;
        }

        $data['html'] = $this->renderPartial('view', ['detail'=>$detail]);

        return $this->asJson($data);

    }
}