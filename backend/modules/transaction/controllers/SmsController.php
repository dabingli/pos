<?php
namespace backend\modules\transaction\controllers;

use common\models\user\SmsCode;
use moonland\phpexcel\Excel;
use yii;

class SmsController extends BaseController
{

    /**
     * 短息发送记录
     *
     * @return string
     */
    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * 短息发送记录列表
     *
     * @return string
     */
    public function actionList()
    {
        $post = $this->request->post();

        $model = SmsCode::find();
        $model->andFilterWhere(['status'=>$post['status']]);
        $model->andFilterWhere(['mobile'=>$post['mobile']]);
        $model->andFilterWhere(['type'=>$post['type']]);

        if ($post['created_at_start']) {
            $model->andFilterWhere([
                '>=',
                'created_at',
                strtotime($post['created_at_start'] . ' 00:00:00')
            ]);
        }
        if ($post['created_at_end']) {
            $model->andFilterWhere([
                '<=',
                'created_at',
                strtotime($post['created_at_end'] . ' 23:59:59')
            ]);
        }

        $model->orderBy(['created_at'=>SORT_DESC]);

        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'code' => $m->code,
                'content' => $m->content,
                'type' => $m->getType(),
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'status' => $m->getStatus(),
                'mobile' => $m->mobile,
                'return_data' => $m->return_data

            ];
        }
        return $this->asJson($data);
    }

    public function export($get){
        $model = SmsCode::find();
        $model->andFilterWhere(['status'=>$get['status']]);
        $model->andFilterWhere(['mobile'=>$get['mobile']]);
        $model->andFilterWhere(['type'=>$get['type']]);

        if ($get['created_at_start']) {
            $model->andFilterWhere([
                '>=',
                'created_at',
                strtotime($get['created_at_start'])
            ]);
        }
        if ($get['created_at_end']) {
            $model->andFilterWhere([
                '<=',
                'created_at',
                strtotime($get['created_at_end'])
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

    /**
     * 导出短信
     *
     * @return string
     */
    public function actionExport(){
        $get = $this->request->get();
        $data = $this->export($get);

        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '短信记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach ($data as $k => $rows) {
            Excel::export([
                'models' => $rows,
                'fileName' => '短信记录',
                'columns' => [
                    [
                        'attribute' => 'code',
                        'header' => '短信编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['code'];
                        }
                    ],
                    [
                        'attribute' => 'mobile',
                        'header' => '接收手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['mobile'];
                        }
                    ],
                    [
                        'attribute' => 'content',
                        'header' => '短信内容',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['content'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '短信类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getType($models['type']);
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
                    [
                        'attribute' => 'status',
                        'header' => '发送状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getStatus($models['status']);
                        }
                    ],
                    [
                        'attribute' => 'return_data',
                        'header' => '失败原因',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['return_data'];
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
    }

    public function getType($type)
    {
        if ($type == 1) {
            return '注册';
        }
        if ($type == 2) {
            return '登录';
        }
        if ($type == 3) {
            return '支付密码';
        }
        if ($type == 4) {
            return '绑卡';
        }
        if($type == 5 ){
            return '忘记密码';
        }
    }

    public function getStatus($status)
    {
        if ($status == 0) {
            return '失败';
        }
        if ($status == 1) {
            return '成功';
        }
    }
}