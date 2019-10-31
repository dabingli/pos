<?php
namespace frontend\modules\order\controllers;

use common\models\user\nestedSets\UserLink;
use common\models\user\User;
use moonland\phpexcel\Excel;
use yii;
use common\helpers\FormHelper;
use common\models\Transaction;

class TransactionController extends \frontend\controllers\MController
{

    const LIMIT = 800000;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $is_search_children = $this->request->post('is_search_children');
        $model = Transaction::find();
        $model->alias('a');
        $model->andFilterWhere([
            'like',
            'a.merchantId',
            $this->request->post('merchantId')
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantName',
            $this->request->post('merchantName')
        ]);
        $model->andFilterWhere([
            'like',
            'a.orderNo',
            $this->request->post('orderNo')
        ]);
        $model->andFilterWhere([
            'like',
            'a.serialNo',
            $this->request->post('serialNo')
        ]);
        if ($this->request->post('user_code') || $this->request->post('real_name')) {
            if($is_search_children == 2)
            {
                $user = User::find();
                $user->andFilterWhere([
                    'user_code' => $this->request->post('user_code')
                ]);
                $user->andFilterWhere([
                    'or',
                    [
                        'like',
                        'user_name',
                        $this->request->post('real_name')
                    ],
                    [
                        'like',
                        'real_name',
                        $this->request->post('real_name')
                    ]
                ]);
                $user = $user->one();

                $userLink = UserLink::findOne([
                    'user_id' => $user->id
                ]);
                $ids_array = [$user->id => $user->id];
                $childrenModel = $userLink->children();
                $childrenModel->andWhere([
                    'agent_id' => Yii::$app->user->identity->agent_id
                ]);
                $childrenModel->select([
                    'user_id'
                ]);

                $children_ids = $childrenModel->indexBy('user_id')->column();
                $ids_array = array_merge($ids_array,$children_ids);

                $model->andWhere([
                    'user_id' =>$ids_array
                ]);
            }else{
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
            }
        } else {
            $model->with('user');
        }
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        if ($this->request->post('created_at_start')) {
            $model->andFilterWhere([
                '>=',
                'a.txTime',
                $this->request->post('created_at_start')
            ]);
        }
        if ($this->request->post('created_at_end')) {
            $model->andFilterWhere([
                '<=',
                'a.txTime',
                $this->request->post('created_at_end') . ' 23:59:59'
            ]);
        }
        $data['total'] = $model->count();
        $model->orderBy([
            'txTime' => SORT_DESC
        ]);
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
                'serialNo' => $m->serialNo
            ];
        }
        
        return $this->asJson($data);
    }

    // 导出
    public function actionExport()
    {
        $get = $this->request->get();
        $type = Transaction::TypeLabels();
        $data = $this->export($get);
        ob_end_clean(); // 清除缓冲区,避免乱码
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        $file = '商户交易记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        $url = Yii::$app->request->referrer;

        foreach ($data as $k => $rows) {
            // var_dump($rows);die;
            Excel::export([
                'models' => $rows,
                'fileName' => '商户交易记录',
                'columns' => [
                    [
                        'attribute' => 'orderNo',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['orderNo'];
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
                            return "\t" . $models['serialNo'];
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
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'transType',
                        'header' => '付款方式',
                        'format' => 'text',
                        'value' => function ($models) use ($type) {
                            return $type[$models['transType']];
                        }
                    ]
                ]
            ]);
        }

        return yii::$app->util->alert('导出成功', $url);
        exit();
    }

    protected function export($get)
    {
        $model = Transaction::find();
        $model->alias('a');
        $model->andFilterWhere([
            'like',
            'a.merchantId',
            $get['merchantId']
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantName',
            $get['merchantName']
        ]);
        $model->andFilterWhere([
            'like',
            'a.orderNo',
            $get['orderNo']
        ]);
        $model->andFilterWhere([
            'like',
            'a.serialNo',
            $get['serialNo']
        ]);
        if ($get['user_code'] || $get['real_name']) {
            $model->with('user');
            if($get['is_search_children'] == 2)
            {
                $user = User::find();
                $user->andFilterWhere([
                    'user_code' => $get['user_code']
                ]);
                $user->andFilterWhere([
                    'or',
                    [
                        'like',
                        'user_name',
                        $get['real_name']
                    ],
                    [
                        'like',
                        'real_name',
                        $get['real_name']
                    ]
                ]);
                $user = $user->one();

                $userLink = UserLink::findOne([
                    'user_id' => $user->id
                ]);
                $ids_array = [$user->id => $user->id];
                $childrenModel = $userLink->children();
                $childrenModel->andWhere([
                    'agent_id' => $user->agent_id
                ]);
                $childrenModel->select([
                    'user_id'
                ]);

                $children_ids = $childrenModel->indexBy('user_id')->column();
                $ids_array = array_merge($ids_array,$children_ids);

                $model->andWhere([
                    'user_id' =>$ids_array
                ]);
            }
            else{
                $model->joinWith([
                    'user' => function ($q) use ($get) {
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
            }
        } else {
            $model->with('user');
        }
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        if ($get['created_at_start']) {
            $model->andFilterWhere([
                '>=',
                'a.txTime',
                $get['created_at_start']
            ]);
        }
        if ($get['created_at_end']) {
            $model->andFilterWhere([
                '<=',
                'a.txTime',
                $get['created_at_end'] . ' 23:59:59'
            ]);
        }
        
        $count = $model->count();
        $limit = self::LIMIT;
        $model->asArray();
        for ($i = 0; $i <= $count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            yield $model->all();
        }
    }

    public function actionView()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            return '';
        }
        
        $data = [];
        
        $transaction = Transaction::findOne([
            'id' => $id,
            'agent_id' => $this->agentId
        ]);
        
        $data['html'] = $this->renderPartial('view', [
            'transaction' => $transaction
        ]);
        
        return $this->asJson($data);
    }
}