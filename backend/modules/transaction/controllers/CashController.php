<?php
namespace backend\modules\transaction\controllers;

use common\models\CashOrder;
use moonland\phpexcel\Excel;
use yii;

class CashController extends BaseController
{

    const LIMIT = 2000;
    /**
     * 代理商提现记录
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 代理商提现记录列表
     *
     * @return string
     */
    public function actionList()
    {
        //获取基信息
        $model = $this->getBaseData('post');

        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));

        $model->asArray();
        //获取收益类型
        $typeLabels = CashOrder::typeLabels();
        $statusLabels = CashOrder::statusLabels();
        $handleLabels = CashOrder::handleLabels();

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'unique_order' => $m['unique_order'],
                'real_name' => $m['real_name'],
                'user_code' => $m['user_code'],
                'mobile' => $m['mobile'],
                'type' => $typeLabels[$m['type']],
                'cash_amount' => $m['cash_amount'],
                'fee' => $m['fee'],
                'account' => $m['account'],
                'account_amount' => $m['account_amount'],
                'cardNo' => $m['cardNo'],
                'cash_provider' => $m['cash_provider'],
                'created_at' => ! empty($m['created_at']) ? date('Y-m-d H:i:s', $m['created_at']) : '',
                'status' => $statusLabels[$m['status']],
                'remarks' => $m['remarks'],
                'handle' => $handleLabels[$m['handle']],
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
        $model = CashOrder::find();
        //关联数据表 agent user
        $model->select('cash_order.*, user.user_code, user.real_name, user.mobile as account');
        $model->leftJoin('user', 'user.id = cash_order.user_id');

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

        //查询条件
        $model->andFilterWhere([
            'and',
            [
                'like',
                'cash_order.unique_order',
                $request->$type('unique_order')
            ],
            'cash_order.type' => $request->$type('type'),
            [
                'like',
                'user.user_code',
                $request->$type('user_code')
            ],
            [
                'like',
                'user.real_name',
                $request->$type('real_name')
            ],
            'user.mobile' => $request->$type('account'),
            'cash_order.status' => $request->$type('status'),
        ]);
        //提现日期
        $created_start = $request->$type('created_at_start');
        if (! empty($created_start)) {
            $model->andWhere([
                '>=',
                'cash_order.created_at',
                strtotime($created_start . '00:00:00')
            ]);
        }
        $created_end = $request->$type('created_at_end');
        if (! empty($created_end)) {
            $model->andWhere([
                '<=',
                'cash_order.created_at',
                strtotime($created_end . '23:59:59')
            ]);
        }
        //排序
        $model->orderby('cash_order.created_at desc');

        return $model;
    }

    public function export(){
        //获取基信息
        $model = $this->getBaseData('get');

        $count = $model->count();
        $limit = self::LIMIT;
        $model->asArray();
        for ($i = 0; $i <= $count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            yield $model->all();
        }
    }

    /**
     * 导出代理商收益记录
     *
     * @return string
     */
    public function actionExport(){
        // $get = $this->request->get();
        $data = $this->export();

        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '代理商收益记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        //收益类型
        foreach ($data as $k => $rows) {
            Excel::export([
                'models' => $rows,
                'fileName' => '代理商收益记录',
                'columns' => [
                    [
                        'attribute' => 'unique_order',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['unique_order'];
                        }
                    ],
                    [
                        'attribute' => 'real_name',
                        'header' => '代理商名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['real_name'];
                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_code'];
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
                            //提现类型
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
                        'header' => '手续费',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['fee'];
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
                            return $models['account'];
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
                            return CashOrder::handleLabels()[$models['status']];
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
                    ],
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
    }
}