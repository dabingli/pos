<?php
namespace frontend\modules\user\controllers;

use common\models\user\User;
use yii;
use common\models\MerchantUser;
use moonland\phpexcel\Excel;
use common\helpers\FormHelper;
use common\models\user\nestedSets\UserLink;
use common\models\AgentProductType;
use common\models\user\UserSettlement;

class MerchantController extends \frontend\controllers\MController
{

    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $is_search_children = $this->request->post('is_search_children');
        $model = MerchantUser::find();
        $model->alias('a');
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'a.merchantId' => $this->request->post('merchantId')
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantName',
            $this->request->post('merchantName')
        ]);
//        $model->andFilterWhere([
//            'a.order' => $this->request->post('orderNo')
//        ]);

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
                        $q->alias('u');
                        $q->andFilterWhere([
                            'like',
                            'u.user_code',
                            $this->request->post('user_code')
                        ]);
                        $q->andFilterWhere([
                            'like',
                            'u.real_name',
                            $this->request->post('real_name')
                        ]);
                    }
                ]);
            }
        }
        if ($this->request->post('bindingTime_start')) {
            $model->andFilterWhere([
                '>=',
                'a.bindingTime',
                strtotime($this->request->post('bindingTime_start'))
            ]);
        }
        if ($this->request->post('bindingTime_end')) {
            $model->andFilterWhere([
                '<=',
                'a.bindingTime',
                strtotime($this->request->post('bindingTime_end') . ' 23:59:59')
            ]);
        }
        $data['total'] = $model->count();
        $offset = $this->request->post('offset');
        $limit = $this->request->post('limit');
        $model->offset($offset)->limit($limit);
        $model->orderBy([
            'bindingTime' => SORT_DESC
        ]);
        $data['rows'] = [];
        foreach ($model->all() as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'merchantId' => $val->merchantId,
                'merchantName' => $val->merchantName,
                'serialNo' => $val->serialNo,
                'agent_name' => $val->user->real_name ?: $val->user->user_name,
                'agent_number' => $val->user->user_code,
                'bindingTime' => ! empty($val->bindingTime) ? date('Y-m-d H:i:s', $val->bindingTime) : ''
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
        $file = '商户信息';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            // var_dump($rows);die;
            Excel::export([
                'models' => $rows,
                'fileName' => '商户信息',
                'columns' => [
                    [
                        'attribute' => 'id',
                        'header' => 'ID',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['id'];
                        }
                    ],
                    /*[
                        'attribute' => 'order',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['order'];
                        }
                    ],*/
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
                        'attribute' => 'phone',
                        'header' => '商户手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['phone'];
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
                        'attribute' => 'agent_name',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['user_name'];
                        }
                    ],
                    [
                        'attribute' => 'real_name',
                        'header' => '代理商姓名',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['real_name'] ?: $models['user']['user_name'];
                        }
                    ],
                    [
                        'attribute' => 'number',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user']['user_code'];
                        }
                    ],
                    [
                        'attribute' => 'bindingTime',
                        'header' => '激活时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['bindingTime']) ? date('Y-m-d H:i:s', $models['bindingTime']) : '';
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
        exit();
    }

    public function export($get)
    {
        $model = MerchantUser::find();
        $model->alias('a');
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'a.merchantId' => $get['merchantId']
        ]);
        $model->andFilterWhere([
            'like',
            'a.merchantName',
            $get['merchantName']
        ]);
        /*$model->andFilterWhere([
            'a.order' => $get['orderNo']
        ]);*/
        $model->andFilterWhere([
            'a.serialNo' => $get['serialNo']
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
                    'in',
                    'user_id',
                    $ids_array
                ]);
            }
            else{
                $model->joinWith([
                    'user' => function ($q) use ($get) {
                        $q->alias('u');
                        $q->andFilterWhere([
                            'u.user_code' => $get['user_code']
                        ]);
                        $q->andFilterWhere([
                            'like',
                            'u.real_name',
                            $get['real_name']
                        ]);
                    }
                ]);
            }

        } else {
            $model->with('user');
        }
        if ($get['bindingTime_start']) {
            $model->andFilterWhere([
                '>=',
                'a.bindingTime',
                strtotime($get['bindingTime_start'])
            ]);
        }
        if ($get['bindingTime_end']) {
            $model->andFilterWhere([
                '<=',
                'a.bindingTime',
                strtotime($get['bindingTime_end'])
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
}