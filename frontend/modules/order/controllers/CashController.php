<?php
namespace frontend\modules\order\controllers;

use common\library\changjie\QueryBalance;
use common\models\agent\Agent;
use common\models\Order;
use common\models\user\User;
use yii;
use common\helpers\FormHelper;
use common\models\CashOrder;
use common\models\user\BankCard;
use moonland\phpexcel\Excel;

class CashController extends \frontend\controllers\MController
{

    const LIMIT = 2000;

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
        $model = CashOrder::find();
        $model->alias('a');
        $model->andFilterWhere([
            'like',
            'a.order',
            $this->request->post('cash_order')
        ]);
        if ($this->request->post('user_code') || $this->request->post('user_name')) {
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
                        $this->request->post('user_name')
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
            'a.type' => $this->request->post('type')
        ]);
        $model->andFilterWhere([
            'a.status' => $this->request->post('status')
        ]);
        $model->andFilterWhere([
            'a.handle' => $this->request->post('handle')
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
        $model->orderBy([
            'created_at' => SORT_DESC
        ]);
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
                'account' => $m->cardNo,
                'mobile' => $m->mobile,
                'type' => $m->getType(),
                'cash_amount' => $m->cash_amount,
                'fee' => $m->fee,
                'account_amount' => $m->account_amount,
                'cash_provider' => $m->cash_provider,
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'status' => $m->getStatus(),
                'remarks' => $m->remarks,
                'handleStr' => $m->getHandle(),
                'handle' => $m->handle,
                'agent_fee' => $m->agent_fee
            ];
        }
        
        return $this->asJson($data);
    }

    // 导出
    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);
        
        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '提现记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            Excel::export([
                'models' => $rows,
                'fileName' => '提现记录',
                'columns' => [
                    [
                        'attribute' => 'order',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['order'];
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
                        'attribute' => 'mobile',
                        'header' => '手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['mobile'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '提现类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            return CashOrder::typeLabels()[$models['type']];
                        }
                    ],
                    [
                        'attribute' => 'cash_amount',
                        'header' => '提现金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['cash_amount'];
                        }
                    ],
                    [
                        'attribute' => 'fee',
                        'header' => '提现手续费',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['fee'];
                        }
                    ],
                    [
                        'attribute' => 'agent_fee',
                        'header' => '平台手续费',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['agent_fee'];
                        }
                    ],
                    [
                        'attribute' => 'account_amount',
                        'header' => '到账金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['account_amount'];
                        }
                    ],
                    [
                        'attribute' => 'account',
                        'header' => '结算账号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['cardNo'];
                        }
                    ],
                    [
                        'attribute' => 'cash_provider',
                        'header' => '提现人',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['cash_provider'];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '提现时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'header' => '交易状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return CashOrder::statusLabels()[$models['status']];
                        }
                    ],
                    [
                        'attribute' => 'remarks',
                        'header' => '交易失败原因',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['remarks'];
                        }
                    ],
                    [
                        'attribute' => 'handle',
                        'header' => '审核状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return CashOrder::handleLabels()[$models['handle']];
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
        $model = CashOrder::find();
        $model->alias('a');
        $model->andFilterWhere([
            'a.order' => $get['cash_order']
        ]);
        if ($get['user_code'] || $get['user_name']) {
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
                        $get['user_name']
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
            'a.type' => $get['type']
        ]);
        $model->andFilterWhere([
            'a.status' => $get['status']
        ]);
        $model->andFilterWhere([
            'a.handle' => $get['handle']
        ]);
        if (! empty($get['created_start'])) {
            $model->andFilterWhere([
                '>=',
                'a.created_at',
                strtotime($get['created_start'])
            ]);
        }
        if (! empty($get['created_end'])) {
            $model->andFilterWhere([
                '<=',
                'a.created_at',
                strtotime($get['created_end']) + 3600 * 24 - 1
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

    // 提现
    public function actionWithdrawal()
    {
        $id = $this->request->post('id');
        $handle = $this->request->post('handle');
        $model = CashOrder::find();
        $model->andWhere([
            'agent_id' => $this->agentId
        ]);
        $model->andWhere([
            'in',
            'id',
            $id
        ]);
        $model->andWhere([
            'handle' => CashOrder::AUDIT_WAIT
        ]);
        $model->with('bankCard');
        $model->asArray();
        
        foreach ($model->all() as $val) {
            if (CashOrder::AUDIT_WAIT != $val['handle'])
                continue;
            
            $data = [];
            
            if (CashOrder::AUDIT_SUCCESS == $handle) {
                
                $cashOrder = new CashOrder([
                    'co_cardNo' => $val['cardNo'],
                    'co_bank' => $val['bank'],
                    'co_money' => $val['account_amount'],
                    'co_OutTradeNo' => $val['order'],
                    'co_bankName' => $val['bankCard']['name'],
                    'co_real_name' => $val['cash_provider'],
                    'co_cash_amount' => $val['cash_amount']
                ]);
                $cashOrder->withdraw();
            } else if (CashOrder::FAIL == $handle) {
                if ($val['type'] == CashOrder::RETURN_CASH) {
                    User::updateAllCounters([
                        'activate_money' => $val['cash_amount']
                    ], [
                        'id' => $val['user_id']
                    ]);
                } else {
                    User::updateAllCounters([
                        'profit_money' => $val['cash_amount']
                    ], [
                        'id' => $val['user_id']
                    ]);
                }
                Agent::updateAllCounters([
                    'balance' => $val['account_amount'] + $this->agentModel->agent_fee
                ], [
                    'id' => $val['agent_id']
                ]);
                $data['status'] = CashOrder::FAIL;
                Order::updateAll([
                    'status' => Order::FAIL
                ], [
                    'order' => $val['order']
                ]);
            }
            
            $data['handle'] = $handle;
            $res = Yii::$app->db->createCommand()
                ->update(CashOrder::tableName(), $data, "id={$val['id']}")
                ->execute();
            if (! $res) {
                Yii::$app->session->setFlash('error', '操作失败');
                return $this->redirect([
                    'index'
                ]);
            }
        }
        
        Yii::$app->session->setFlash('success', '操作成功');
        return $this->redirect([
            'index'
        ]);
    }
}