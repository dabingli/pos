<?php
namespace backend\modules\app\controllers;

use yii;
use common\helpers\FormHelper;
use common\models\app\AppFeedBack;
use moonland\phpexcel\Excel;
use backend\modules\app\controllers\BaseController;

class FeedbackController extends BaseController
{

    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = AppFeedback::find();
        $model->with('app');
        $data = [];
        $data['total'] = $model->count();
        $model->orderBy(['created_at'=>SORT_DESC]);
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        $share = $model->all();
        foreach ($share as $val) {
            // var_dump($val->app);die;
            $data['rows'][] = [
                'id' => $val->id,
                'app_name' => $val->app->name,
                'type' => $val->getType(),
                'description' => $val->description,
                'name' => $val->name,
                'created_at' => date('Y-m-d H:i:s', $val->created_at)
            ];
        }
        return $this->asJson($data);
    }

    protected function export()
    {
        $model = AppFeedback::find();
        $model->with('app');
        // $model->andWhere($where);
        $count = $model->count();
        $limit = self::LIMIT;
        $model->asArray();
        for ($i = 0; $i <= $count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            yield $model->all();
        }
    }

    public function actionExport()
    {
        $data = $this->export();
        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '问题反馈';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            Excel::export([
                'models' => $rows,
                'fileName' => '问题反馈',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'header' => 'app名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['app']['name'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '问题类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getType($models['type']);
                        }
                    ],
                    [
                        'attribute' => 'description',
                        'header' => '问题描述',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['description'];
                        }
                    ],
                    [
                        'attribute' => 'name',
                        'header' => '反馈人',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['name'];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '反馈时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
        exit();
    }

    // 获取问题类型
    protected function getType($type)
    {
        if ($type == 1) {
            return '性能问题';
        }
        if ($type == 2) {
            return '功能问题';
        }
        if ($type == 3) {
            return '交互问题';
        }
        if ($type == 4) {
            return '其他问题';
        }
    }
}