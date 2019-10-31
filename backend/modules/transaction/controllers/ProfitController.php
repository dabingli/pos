<?php
namespace backend\modules\transaction\controllers;

use common\models\Profit;
use moonland\phpexcel\Excel;
use yii;

class ProfitController extends BaseController
{

    const LIMIT = 2000;
    /**
     * 代理商收益记录
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 代理商收益记录列表
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
        $model->orderby('profit.created_at desc');

        $model->asArray();
        //获取收益类型
        $typeLabels = Profit::typeLabels();
        
        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m['id'],
                'unique_order' => $m['unique_order'],
                'merchantId' => $m['merchantId'],
                'merchantName' => $m['merchantName'],
                'user_code' => $m['user_code'],
                'real_name' => $m['real_name'],
                'serialNo' => $m['serialNo'],
                'amount' => $m['transaction_amount'],
                'type' => $typeLabels[$m['type']],
                'amount_profit' => $m['amount_profit'],
                'created_at' => ! empty($m['created_at']) ? date('Y-m-d H:i:s', $m['created_at']) : '',
                'agent_name' => $m['agent']['name']
            ];
        }
        return $this->asJson($data);
    }

    /**
     * 基础信息
     * type 类型分为 post 和 get
     * return model
     */
    private function getBaseData($type='post'){
        $request = $this->request;
        $model = Profit::find();
        //关联数据表 agent user
        $model->select('profit.*, user.user_code, user.real_name');
        $model->leftJoin('user', 'user.id = profit.user_id');

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
                'profit.unique_order',
                $request->$type('unique_order')
            ],
            [
                'profit.type' => $request->$type('type')
            ],
            [
                'like',
                'profit.serialNo',
                $request->$type('serialNo')
            ],
            [
                'like',
                'profit.merchantId',
                $request->$type('merchantId')
            ],
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
        ]);
        //收益时间
        $created_start = $request->$type('created_at_start');
        if (! empty($created_start)) {
            $model->andWhere([
                '>=',
                'profit.created_at',
                strtotime($created_start . '00:00:00')
            ]);
        }
        $created_end = $request->$type('created_at_end');
        if (! empty($created_end)) {
            $model->andWhere([
                '<=',
                'profit.created_at',
                strtotime($created_end . '23:59:59')
            ]);
        }
        //排序
        $model->orderby('profit.created_at desc');

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
                        'attribute' => 'serialNo',
                        'header' => '机具编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['serialNo'];
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
                        'value' => function ($models) {
                            //获取收益类型
                            return Profit::typeLabels()[$models['type']];
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
                        'header' => '发送时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
    }
    //详情
    public function actionView()
    {
        $id = $this->request->get('id');
        if(empty($id)){
            return '';
        }

        $data = [];

        $detail = Profit::findOne(['id'=>$id]);

        if(empty($detail)){
            return null;
        }

        $data['html'] = $this->renderPartial('view', ['detail'=>$detail]);

        return $this->asJson($data);

    }
}