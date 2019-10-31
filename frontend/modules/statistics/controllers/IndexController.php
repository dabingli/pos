<?php
namespace frontend\modules\statistics\controllers;

use common\models\Transaction;
use yii;
use common\helpers\FormHelper;
use common\models\user\User;
use common\models\statistics\User as StatisticsUser;
use common\models\Profit;
use common\models\MerchantUser;
use moonland\phpexcel\Excel;

class IndexController extends \frontend\controllers\MController
{
    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = StatisticsUser::find();
        $model->andWhere([
            'agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'like',
            'user_code',
            $this->request->post('user_code')
        ]);
        $model->andFilterWhere(['or',
            [
                'like',
                'real_name',
                $this->request->post('real_name')
            ],
            [
                'like',
                'user_name',
                $this->request->post('real_name')
            ]
        ]);
        $model->andFilterWhere([
            'like',
            'mobile',
            $this->request->post('mobile')
        ]);
        
        $data['total'] = $model->count();
        $limit = $this->request->post('limit');
        $offset = $this->request->post('offset');
        $model->limit($limit)->offset($offset);
        $data['rows'] = [];
        $model->orderBy([
            'id' => SORT_ASC
        ]);
        foreach ($model->all() as $m) {
            // 交易金额start
            $ids_array = [$m->id => $m->id];
            $userLink = $m->userLink;
            $childrenModel = $userLink->children();
            $children_ids = $childrenModel->indexBy('user_id')->column();
            $ids_array = array_merge($ids_array,$children_ids);

            $totalMoney = Transaction::find();
            if (! empty($this->request->post('created_start'))) {
                $totalMoney->andFilterWhere([
                    '>=',
                    'created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $totalMoney->andFilterWhere([
                    '<=',
                    'created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $totalMoney->andWhere(['user_id'=>$ids_array]);
            $totalMoney = $totalMoney->sum('txAmt');
            // 交易金额end
            
            // 返现收益start
            $profitMoney = $m->getProfitMoneyOne();
            if (! empty($this->request->post('created_start'))) {
                $profitMoney->andFilterWhere([
                    '>=',
                    'created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $profitMoney->andFilterWhere([
                    '<=',
                    'created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $profitMoney->andWhere([
                'entry' => Profit::ENTRY,
                'type' => [
                    Profit::TRANSACTION_DISTRIBUTION,
                    Profit::FROZEN_DISTRIBUTION
                ]
            ]);
            $profitMoney = $profitMoney->sum('amount_profit');
            // 返现收益end
            
            // 激活返现收益start
            $activateMoney = $m->getProfitMoneyOne();
            if (! empty($this->request->post('created_start'))) {
                $activateMoney->andFilterWhere([
                    '>=',
                    'created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $activateMoney->andFilterWhere([
                    '<=',
                    'created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $activateMoney->andWhere([
                'entry' => Profit::ENTRY,
                'type' => [
                    Profit::ACTIVATION_RETURN,
                    Profit::FROZEN_RETURN
                ]
            ]);
            $activateMoney = $activateMoney->sum('amount_profit');
            // 激活返现收益end
            
            // 直属下级数量start
            $sonCount = $m->getSonCount();
            if (! empty($this->request->post('created_start'))) {
                $sonCount->andFilterWhere([
                    '>=',
                    'created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $sonCount->andFilterWhere([
                    '<=',
                    'created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $sonCount = $sonCount->count();

            // 直属下级数量end
            
            // 子子级无限子级数量start
            $userLink = $m->userLink;
            $sonsCount = $userLink->children();
            
            if (! empty($this->request->post('created_start'))) {
                $sonsCount->andFilterWhere([
                    '>=',
                    'created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $sonsCount->andFilterWhere([
                    '<=',
                    'created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $sonsCount = $sonsCount->count();
            // 子子级无限子级数量end
            
            // 下级直营商户start
            $merchant = MerchantUser::find()->andWhere(['agent_id'=>$this->agentId,'user_id'=>$m->id]);
//            $merchant->innerJoin([
//                'b' => MerchantUser::tableName()
//            ], 'a.id=b.user_id')->alias('a');
            $merchant->alias('b');
            if (! empty($this->request->post('created_start'))) {
                $merchant->andFilterWhere([
                    '>=',
                    'b.created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $merchant->andFilterWhere([
                    '<=',
                    'b.created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $merchant = $merchant->count();
            // 下级直营商户end
            
            // 下级全部商户start
            $userLink = $m->userLink;
            $merchants = $userLink->children();
            $merchants->innerJoin([
                'b' => MerchantUser::tableName()
            ], 'a.user_id=b.user_id')->alias('a');
            if (! empty($this->request->post('created_start'))) {
                $merchants->andFilterWhere([
                    '>=',
                    'b.created_at',
                    strtotime($this->request->post('created_start'))
                ]);
            }
            if (! empty($this->request->post('created_end'))) {
                $merchants->andFilterWhere([
                    '<=',
                    'b.created_at',
                    strtotime($this->request->post('created_end') . '23:59:59')
                ]);
            }
            $merchants = $merchants->count();
            // 下级全部商户end
            
            $data['rows'][] = [
                'id' => $m->id,
                'user_code' => $m->user_code,
                'real_name' => !empty($m->real_name) ? $m->real_name : $m->user_name,
                'mobile' => $m->mobile,
                'total_money' => $totalMoney ? sprintf("%.2f", $totalMoney) : 0,
                'activate_money' => $activateMoney ? sprintf("%.2f", $activateMoney) : 0,
                'profit_money' => $profitMoney ? sprintf("%.2f", $profitMoney) : 0,
                'son' => $sonCount ? $sonCount : 0,
                'sons' => $sonsCount ? $sonsCount : 0,
                'merchant' => $merchant ? $merchant : 0,
                'merchants' => $merchant + $merchants ? $merchant + $merchants : 0
            ];
        }
        return $this->asJson($data);
    }

    //    导出
    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);

        $url =  Yii::$app->request->referrer;
        ob_end_clean();//清除缓冲区,避免乱码
        $file = '代理商交易统计';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach($data as $k => $rows)
        {
            Excel::export([
                'models' => $rows,
                'fileName' => '代理商交易统计',
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
                        'attribute' => 'real_name',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['real_name'];

                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '商户编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_code'];

                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '商户名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_name'];
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
                        'attribute' => 'total_money',
                        'header' => '交易金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['total_money'];
                        }
                    ],
                    [
                        'attribute' => 'activate_money',
                        'header' => '返现收益',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['activate_money'];
                        }
                    ],
                    [
                        'attribute' => 'profit_money',
                        'header' => '分润收益',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['profit_money'];
                        }
                    ],
                    [
                        'attribute' => 'total_money',
                        'header' => '总收益',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['activate_money'] + $models['profit_money'];
                        }
                    ],
                    [
                        'attribute' => 'son',
                        'header' => '直属代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['son'];
                        }
                    ],
                    [
                        'attribute' => 'sons',
                        'header' => '全部代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['sons'];
                        }
                    ],
                    [
                        'attribute' => 'merchant',
                        'header' => '直营商户',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['merchant'];
                        }
                    ],
                    [
                        'attribute' => 'merchants',
                        'header' => '全部商户',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['merchants'];
                        }
                    ]
                ]
            ]);
        }

        return yii::$app->util->alert('导出成功',$url);
        exit;
    }

    protected function export($get)
    {
        $model = StatisticsUser::find();
        $model->andWhere([
            'agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'user_code' => $get['user_code']
        ]);
        $model->andFilterWhere([
            'like',
            'real_name',
            $get['real_name']
        ]);
        $model->andFilterWhere([
            'like',
            'mobile',
            $get['mobile']
        ]);

        $count=$model->count();
        $model->orderBy([
            'id' => SORT_ASC
        ]);
        $limit=self::LIMIT;
        for($i=0;$i<=$count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            $data = [];
            foreach ($model->all() as $m) {
                // 交易金额start
                $ids_array = [$m->id => $m->id];
                $userLink = $m->userLink;
                $childrenModel = $userLink->children();
                $children_ids = $childrenModel->indexBy('user_id')->column();
                $ids_array = array_merge($ids_array,$children_ids);

                $totalMoney = Transaction::find();
                if (!empty($get['created_start'])) {
                    $totalMoney->andFilterWhere([
                        '>=',
                        'created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (!empty($get['created_end'])) {
                    $totalMoney->andFilterWhere([
                        '<=',
                        'created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $totalMoney->andWhere(['user_id'=>$ids_array]);
                $totalMoney = $totalMoney->sum('txAmt');
                // 交易金额end

                // 返现收益start
                $profitMoney = $m->getProfitMoneyOne();
                if (!empty($get['created_start'])) {
                    $profitMoney->andFilterWhere([
                        '>=',
                        'created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (!empty($get['created_end'])) {
                    $profitMoney->andFilterWhere([
                        '<=',
                        'created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $profitMoney->andWhere([
                    'entry' => Profit::ENTRY,
                    'type' => [
                        Profit::TRANSACTION_DISTRIBUTION,
                        Profit::FROZEN_DISTRIBUTION
                    ]
                ]);
                $profitMoney = $profitMoney->sum('amount_profit');
                // 返现收益end

                // 激活返现收益start
                $activateMoney = $m->getProfitMoneyOne();
                if (!empty($get['created_start'])) {
                    $activateMoney->andFilterWhere([
                        '>=',
                        'created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (!empty($get['created_end'])) {
                    $activateMoney->andFilterWhere([
                        '<=',
                        'created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $activateMoney->andWhere([
                    'entry' => Profit::ENTRY,
                    'type' => [
                        Profit::ACTIVATION_RETURN,
                        Profit::FROZEN_RETURN
                    ]
                ]);
                $activateMoney = $activateMoney->sum('amount_profit');
                // 激活返现收益end

                // 直属下级数量start
                $sonCount = $m->getSonCount();
                if (!empty($get['created_start'])) {
                    $sonCount->andFilterWhere([
                        '>=',
                        'created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (!empty($get['created_end'])) {
                    $sonCount->andFilterWhere([
                        '<=',
                        'created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $sonCount = $sonCount->count();

                // 直属下级数量end

                // 子子级无限子级数量start
                $userLink = $m->userLink;
                $sonsCount = $userLink->children();

                if (!empty($get['created_start'])) {
                    $sonsCount->andFilterWhere([
                        '>=',
                        'created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (!empty($get['created_end'])) {
                    $sonsCount->andFilterWhere([
                        '<=',
                        'created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $sonsCount = $sonsCount->count();
                // 子子级无限子级数量end

                // 下级直营商户start
                $merchant = MerchantUser::find()->andWhere(['agent_id'=>$this->agentId,'user_id'=>$m->id]);
//            $merchant->innerJoin([
//                'b' => MerchantUser::tableName()
//            ], 'a.id=b.user_id')->alias('a');
                $merchant->alias('b');
                if (! empty($get['created_start'])) {
                    $merchant->andFilterWhere([
                        '>=',
                        'b.created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (! empty($get['created_end'])) {
                    $merchant->andFilterWhere([
                        '<=',
                        'b.created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $merchant = $merchant->count();
                // 下级直营商户end

                // 下级全部商户start  不包括直属商户
                $userLink = $m->userLink;
                $merchants = $userLink->children();
                $merchants->innerJoin([
                    'b' => MerchantUser::tableName()
                ], 'a.user_id=b.user_id')->alias('a');
                if (! empty($get['created_start'])) {
                    $merchants->andFilterWhere([
                        '>=',
                        'b.created_at',
                        strtotime($get['created_start'])
                    ]);
                }
                if (! empty($get['created_end'])) {
                    $merchants->andFilterWhere([
                        '<=',
                        'b.created_at',
                        strtotime($get['created_end'] . '23:59:59')
                    ]);
                }
                $merchants = $merchants->count();
                $merchants += $merchant;
                // 下级全部商户end

                $data[] = [
                    'id' => $m->id,
                    'user_code' => $m->user_code,
                    'user_name' => $m->user_name,
                    'real_name' => $m->real_name,
                    'mobile' => $m->mobile,
                    'total_money' => $totalMoney ? sprintf("%.2f", $totalMoney) : 0,
                    'activate_money' => $activateMoney ? sprintf("%.2f", $activateMoney) : 0,
                    'profit_money' => $profitMoney ? sprintf("%.2f", $profitMoney) : 0,
                    'son' => $sonCount ? $sonCount : 0,
                    'sons' => $sonsCount ? $sonsCount : 0,
                    'merchant' => $merchant ? $merchant : 0,
                    'merchants' => $merchants ? $merchants : 0
                ];
            }
            yield $data;
        }

    }
}