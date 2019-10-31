<?php
namespace frontend\modules\product\controllers;

use common\models\user\nestedSets\UserLink;
use yii;
use common\models\AgentProductType;
use common\models\product\ProductType;
use common\models\product\Product;
use common\models\product\ProductLog;
use common\models\user\User;
use moonland\phpexcel\Excel;

class ProductController extends \frontend\controllers\MController
{

    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model_name = $this->request->post('model');
        $user_name = $this->request->post('user_name');
        $expire_time_start = $this->request->post('expire_time_start');
        $expire_time_end = $this->request->post('expire_time_end');
        $activate_time_start = $this->request->post('activate_time_start');
        $activate_time_end = $this->request->post('activate_time_end');
        $product_no_start = $this->request->post('product_no_start');
        $product_no_end = $this->request->post('product_no_end');
        $activate_status = $this->request->post('activate_status');
        $status = $this->request->post('status');
        $send_time_start = $this->request->post('send_time_start');
        $send_time_end = $this->request->post('send_time_end');
        $is_search_children = $this->request->post('is_search_children');
        
//         var_dump(Yii::$app->services->agent);die;
        $model = Product::find();
        $model->alias('p');
        $model->andFilterWhere([
            'model' => $model_name
        ]);

//        在库时status=1或者status=4和机具在一级代理商下
//        已下发时status=2或者status=4和机具不在一级代理商下
        if($status == Product::IN_STORE)
        {
            $model->andWhere([
                'or',
                [
                    'status'=>Product::IN_STORE
                ],
                [
                    'user_id' => $this->agentAppUser->id,
                    'status' => Product::NO_SEND
                ]
            ]);
        }
        elseif($status == Product::SEND)
        {
            $model->andWhere([
                'or',
                [
                    'status'=>Product::SEND
                ],
                [
                    'and',
                    [
                        '!=',
                        'user_id',
                        $this->agentAppUser->id,
                    ],
                    'status' => Product::NO_SEND
                ]
            ]);
        }else{
            $model->andFilterWhere([
                'status' => $status
            ]);
        }

        $model->andFilterWhere([
            'activate_status' => $activate_status
        ]);
        
        $model->andWhere([
            'p.agent_id' => $this->agentId
        ]);
        
        // 到期日期
        if (! empty($expire_time_start)) {
            $model->andWhere([
                '>=',
                'expire_time',
                strtotime($expire_time_start)
            ]);
        }if (! empty($expire_time_end)) {
            $model->andWhere([
                '<=',
                'expire_time',
                strtotime($expire_time_end . '23:59:59')
            ]);
        }

        if (!empty($user_name)) {
            if($is_search_children == 2)
            {
                $user = User::find();
                $user->andFilterWhere([
                    'or',
                    [
                        'like',
                        'user_name',
                        $this->request->post('user_name')
                    ],
                    [
                        'like',
                        'real_name',
                        $this->request->post('user_name')
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
                    'productUser' => function ($q) use($user_name){
                        $q->alias('u');
                        $q->andFilterWhere(['or',['like','u.user_name',$user_name],['like','real_name',$user_name]]);
                    }
                ]);
            }
        } else {
            $model->with('productUser');
        }
        
        // 激活时间
       if (! empty($activate_time_start)) {
            $model->andWhere([
                '>=',
                'activate_time',
                strtotime($activate_time_start)
            ]);
        }
        if (! empty($activate_time_end)) {
            $model->andWhere([
                '<=',
                'activate_time',
                strtotime($activate_time_end . '23:59:59')
            ]);
        }

        // 机型编号
        if (! empty($product_no_start)) {
            $model->andWhere([
                '>=',
                'product_no',
                $product_no_start,
            ]);
        }
        if (! empty($product_no_end)) {
            $model->andWhere([
                '<=',
                'product_no',
                $product_no_end
            ]);
        }
        
        // 下发时间
        if (! empty($send_time_start)) {
            $model->andWhere([
                '>=',
                'send_time',
                strtotime($send_time_start)
            ]);
        }if (! empty($send_time_end)) {
            $model->andWhere([
                '<=',
                'send_time',
                strtotime($send_time_end . '23:59:59')
            ]);
        }
        
        $model->with(['agentProductType'=>function($q){
            $q->with('productType');
        }]);

        $model->orderBy(['store_time'=>SORT_DESC,'product_no'=>SORT_ASC]);
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));

//        var_dump($data['total']);die;
//        var_dump($model->createCommand()->getRawSql());die;

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            /**
             * 1级代理商 机具user等于自己id并且status等于4则是入库状态
             * 不是1级代理商 status为4 改为已下发状态
             */
            if($m->status == Product::NO_SEND){
                if($m->user_id == $this->agentAppUser->id){
                    $m->status = Product::IN_STORE;
                }else{
                    $m->status = Product::SEND;
                }
            }

            $data['rows'][] = [
                'id' => $m->id,
                'product_no' => $m->product_no,
                'type' => $m->agentProductType->productType->name,
                'model' => $m->model,
                'store_time' => ! empty($m->store_time) ? date('Y-m-d', $m->store_time) : '',
                'expire_time' => ! empty($m->expire_time) ? date('Y-m-d', $m->expire_time) : '',
                'user_code' => $m->productUser->user_code,
                'user_name' => !empty($m->productUser->real_name) ? $m->productUser->real_name : $m->productUser->user_name,
                'activate_status' => $m->getActivateStatus(),
                'frost_status' => $m->getFrostStatus(),
                'activate_time' => ! empty($m->activate_time) ? date('Y-m-d H:i:s', $m->activate_time) : '',
                'status' => $m->getStatus(),
                'send_time' => ! empty($m->send_time) ? date('Y-m-d H:i:s', $m->send_time) : '',
                'refund_time' => ! empty($m->refund_time) ? date('Y-m-d', $m->refund_time) : ''
            ];
        }
        return $this->asJson($data);
    }

    public function actionStore()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::find();
            $model->select([
                'id',
                'product_type_id'
            ]);
            $model->andWhere([
                'agent_id' => $this->agentId
            ]);
            $model->with('productType');
            $model->asArray();
            $product_type = $model->all();
            $type = array();
            foreach ($product_type as $key => $val) {
                $type[$key]['name'] = $val['productType']['name'];
                $type[$key]['id'] = $val['id'];
            }
            $number = ProductLog::getSerial(Product::IN_STORE);

            $data['html'] = $this->renderPartial('store', [
                'type' => $type,
                'number' => $number
            ]);
        }
        return $this->asJson($data);
    }

    // 入库
    public function actionStoreAdd()
    {
        $post = $this->request->post();
        $post['store_time'] = strtotime($post['store_time']);
        $post['expire_time'] = strtotime($post['expire_time']);
//         var_dump($post);die;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($post['product_no_start'] as $key => $val) {

                if(strlen($post['product_no_start'][$key]) != strlen($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key].'和机具编号为'.$post['product_no_end'][$key].'长度不一样');
                }

                if(intval($post['product_no_start'][$key]) > intval($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key] . '大于机具编号'.$post['product_no_end'][$key]);
                }

                for ($i = 0; $i < $post['product_amount'][$key]; $i ++) {
                    $product_no_data[$i] = $post['product_no_start'][$key];
                    $data[$i]['product_no'] = $post['product_no_start'][$key];
                    $data[$i]['type_id'] = $post['type'][$key];
                    $data[$i]['store_time'] = $post['store_time'];
                    $data[$i]['model'] = $post['model'][$key];
                    $data[$i]['expire_time'] = $post['expire_time'];
                    $data[$i]['status'] = Product::IN_STORE;
                    $post['product_no_start'][$key] = sprintf("%0".strlen($post['product_no_start'][$key])."d", $post['product_no_start'][$key]+1);
                    $data[$i]['agent_id'] = $this->agentId;
                    $data[$i]['user_id'] = $this->agentAppUser->id;
                    $data[$i]['user_code'] = $this->agentAppUser->user_code;
                    $data[$i]['user_name'] = $this->agentAppUser->user_name;
                }

                $product = Product::find()->andWhere(['in','product_no',$product_no_data])->one();
                if(!empty($product))
                {
                    $transaction->rollBack();
                    return $this->asJson([
                        'status' => -1,
                        'msg' => '机具编号为'.$product->product_no.'的机具已存在',
                        'data' => ''
                    ]);
                }
//                var_dump($product);
                unset($product_no_data);

                $success = Yii::$app->db->createCommand()
                    ->batchInsert(Product::tableName(), [
                    'product_no',
                    'type_id',
                    'store_time',
                    'model',
                    'expire_time',
                    'status',
                    'agent_id',
                    'user_id',
                    'user_code',
                    'user_name'
                ], $data)
                    ->execute();

                $arr['serial'] = $post['serial'];
                $arr['name'] = $post['name'];

                array_walk($data, function (&$value, $key, $arr) {
                    $value = array_merge($value, $arr);
                }, $arr);

                $result = Yii::$app->db->createCommand()
                    ->batchInsert(ProductLog::tableName(), [
                        'product_no',
                        'agent_product_type_id',
                        'store_time',
                        'model',
                        'expire_time',
                        'status',
                        'agent_id',
                        'user_id',
                        'user_code',
                        'user_name',
                        'serial',
                        'name'
                    ], $data)
                    ->execute();

                unset($data);
            }
            
            if ($success == true && $result) {
                $transaction->commit();
//                Yii::$app->session->setFlash('success', '入库成功');
                return $this->asJson([
                    'status' => 1,
                    'msg' => '入库成功',
                    'data' => ''
                ]);
            }else{
                $transaction->rollBack();
                return $this->asJson([
                    'status' => 0,
                    'msg' => '入库失败',
                    'data' => ''
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionRefund()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            
            $number = ProductLog::getSerial(Product::REFUND);
            $data['html'] = $this->renderPartial('refund', [
                'number' => $number
            ]);
        }
        return $this->asJson($data);
    }

    // 搜索机具类型
    public function actionSearchType()
    {
        $product_no = $this->request->post('product_no');
        $model = Product::find();
        $model->andWhere([
            'product_no' => $product_no
        ]);
        $model->with(['agentProductType'=>function($q){
            $q->with('productType');
        }]);
        $data = $model->asArray()->all();
        if (empty($data)) {
            return false;
        }
//         var_dump($data);die;
        return $this->asJson($data);
    }

    // 退货
    public function actionRefundDo()
    {
        $post = $this->request->post();
        $post['refund_time'] = strtotime($post['refund_time']);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($res = Product::existProductOrder($post['product_no_start'], $post['product_amount'])){
                throw new \Exception('退货的机具中已有交易订单，无法退货');
            }

            foreach ($post['product_no_start'] as $key => $val) {

                if(strlen($post['product_no_start'][$key]) != strlen($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key].'和机具编号为'.$post['product_no_end'][$key].'长度不一样');
                }

                if(intval($post['product_no_start'][$key]) > intval($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key] . '大于机具编号'.$post['product_no_end'][$key]);
                }


                $condition = [
                    'and',
                    ['activate_status' => Product::NO],
                    [
                        'user_id' => $this->agentAppUser->id
                    ],
                    [
                        'between',
                        'product_no',
                        $post['product_no_start'][$key],
                        $post['product_no_end'][$key],
                    ]
                ];
                $result = Product::updateAll([
                    'status' => Product::REFUND,
                    'refund_time' => $post['refund_time']
                ], $condition);

                for ($i = 0; $i < $post['product_amount'][$key]; $i ++) {
                    $data[$i]['product_no'] = $post['product_no_start'][$key];
                    $data[$i]['agent_product_type_id'] = $post['agent_product_type_id'][$key];
                    $data[$i]['refund_time'] = $post['refund_time'];
                    $data[$i]['model'] = $post['model'][$key];
                    $data[$i]['status'] = Product::REFUND;
                    $post['product_no_start'][$key] = sprintf("%0".strlen($post['product_no_start'][$key])."d", $post['product_no_start'][$key]+1);
                    $data[$i]['agent_id'] = $this->agentId;
                    $data[$i]['user_id'] = $this->agentAppUser->id;
                    $data[$i]['serial'] = $post['serial'];
                    $data[$i]['name'] = $post['name'];
                }

                $success = Yii::$app->db->createCommand()
                    ->batchInsert(ProductLog::tableName(), [
                        'product_no',
                        'agent_product_type_id',
                        'refund_time',
                        'model',
                        'status',
                        'agent_id',
                        'user_id',
                        'serial',
                        'name'
                    ], $data)
                    ->execute();

                unset($data);
            }

            if ($result == true && $success) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', '退货成功');
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '退货失败');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionAgainStore()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {

            $number = 'AS' . date('Ymd') . rand(10000, 99999);
            $data['html'] = $this->renderPartial('again-store', [
                'number' => $number
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAgainStoreDo()
    {
        $post = $this->request->post();
        $post['store_time'] = strtotime($post['store_time']);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $productNos = Product::getProductNos($post['product_no_start'], $post['product_amount']);
            $productModel = product::find();
            $productModel -> where(['in', 'product_no', $productNos]);
            $productModel -> andWhere(['agent_id'=>$this->agentId]);
            $productModel -> andWhere(['status'=>Product::REFUND]);
            $count = $productModel -> count();
            if(count($productNos) != $count) {
                throw new \Exception('只有是您退货的机具才能重新入库，请重新输入机具编号');
            }
            $product = $productModel->all();

            // 重新入库
            $condition = [
                'product_no'=>$productNos,
                'status'=>Product::REFUND
            ];
            $update = [
                'status'=>Product::IN_STORE,
                'user_id' => $this->agentAppUser->id,
                'user_code' => $this->agentAppUser->user_code,
                'user_name' => $this->agentAppUser->user_name
            ];
            $result = Product::updateAll($update, $condition);
            if ($result != true) {
                throw new \Exception('机具状态更新失败');
            }

            // 入库记录
            $productLog = [];
            foreach ($product as $k=>$v) {
                $productLog[] = [
                    'store_time' => $post['store_time'],
                    'name' => $post['name'],
                    'serial' => $post['serial'],
                    'user_id' => $this->agentAppUser->id,
                    'user_name' => $this->agentAppUser->user_name,
                    'user_code' => $this->agentAppUser->user_code,
                    'status' => Product::IN_STORE,
                    'expire_time' => $v['expire_time'],
                    'product_no' => $v['product_no'],
                    'agent_product_type_id' => $v['type_id'],
                    'mobile' => $v['mobile'],
                    'activate_status' => $v['activate_status'],
                    'activate_time' => $v['activate_time'],
                    'agent_id' => $v['agent_id'],
                    'model' => $v['model'],
                ];
            }

            $success = Yii::$app->db->createCommand()
                ->batchInsert(ProductLog::tableName(), [
                    'store_time',
                    'name',
                    'serial',
                    'user_id',
                    'user_name',
                    'user_code',
                    'status',
                    'expire_time',
                    'product_no',
                    'agent_product_type_id',
                    'mobile',
                    'activate_status',
                    'activate_time',
                    'agent_id',
                    'model',
                ], $productLog)
                ->execute();

            if (!$success) {
                throw new \Exception('机具添加入库记录失败');
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', '重新入库成功');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::find();
            $model->select([
                'id',
                'product_type_id'
            ]);
            $model->andWhere([
                'agent_id' => $this->agentId
            ]);
            $model->with('productType');
            $model->asArray();
            $product_type = $model->all();
            $type = array();
            foreach ($product_type as $key => $val) {
                $type[$key]['name'] = $val['productType']['name'];
                $type[$key]['id'] = $val['id'];
            }
            $data['html'] = $this->renderPartial('edit', [
                'type' => $type
            ]);
        }
        return $this->asJson($data);
    }

    // 修改
    public function actionEditDo()
    {
        $post = $this->request->post();
        
        $condition = [
            // ['!=','status',3],
            'and',
            [
                '!=',
                'status',
                3
            ],
            [
                'between',
                'product_no',
                $post['product_no_start'],
                $post['product_no_end']
            ],
            [
                'agent_id' => $this->agentAppUser->agent_id,
                'user_id' => $this->agentAppUser->id
            ]
        ];
        $data = [
            'model' => $post['model'],
            'type_id' => $post['type'],
            'store_time' => strtotime($post['store_time']),
            'expire_time' => strtotime($post['expire_time'])
        ];
        // var_dump($data);die;
        $result = Product::updateAll($data, $condition);
        if ($result == true) {
            Yii::$app->session->setFlash('success', '修改成功');
        }else{
            Yii::$app->session->setFlash('error', '机具不在库，修改失败');
        }
        return $this->redirect([
            'index'
        ]);
        // var_dump($post);die;
    }

    public function actionBack()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::find();
            $model->select([
                'id',
                'product_type_id'
            ]);
            $model->andWhere([
                'agent_id' => $this->agentId
            ]);
            $model->with('productType');
            $model->asArray();
            $product_type = $model->all();
            $type = array();
            foreach ($product_type as $key => $val) {
                $type[$key]['name'] = $val['productType']['name'];
                $type[$key]['id'] = $val['productType']['id'];
            }
            $number = ProductLog::getSerial(Product::NO_SEND);
            $data['html'] = $this->renderPartial('back', [
                'number' => $number,
                'type' => $type
            ]);
        }
        return $this->asJson($data);
    }

    // 回拨
    public function actionBackDo()
    {
        $post = $this->request->post();
        $post['back_time'] = time();
        $transaction = Yii::$app->db->beginTransaction();
        // var_dump($post);die;
        try {
            if($res = Product::existProductOrder($post['product_no_start'], $post['product_amount'])){
                throw new \Exception('回拨的机具中已有交易订单，无法回拨');
            }

            foreach ($post['product_no_start'] as $key => $val) {

                if(strlen($post['product_no_start'][$key]) != strlen($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key].'和机具编号为'.$post['product_no_end'][$key].'长度不一样');
                }

                if(intval($post['product_no_start'][$key]) > intval($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key] . '大于机具编号'.$post['product_no_end'][$key]);
                }


                $condition = [
                    'and',
                    [
                        '!=',
                        'activate_status',
                        Product::YES
                    ],
                    [
                        'between',
                        'product_no',
                        $post['product_no_start'][$key],
                        $post['product_no_end'][$key]
                    ],
                    [
                        'agent_id' => $this->agentAppUser->agent_id
                    ]
                ];

                $product = Product::find()->andWhere(['between','product_no',$post['product_no_start'][$key],$post['product_no_end'][$key]])->all();
                for ($i = 0; $i < $post['product_amount'][$key]; $i ++) {
                    foreach($product as $k =>$v)
                    {
                        $data[$i]['mobile'] = $v->mobile;
                        $data[$i]['user_name'] = $v->user_name;
                        $data[$i]['user_code'] = $v->user_code;
                    }

                    $data[$i]['product_no'] = $post['product_no_start'][$key];
                    $data[$i]['agent_product_type_id'] = $post['agent_product_type_id'][$key];
                    $data[$i]['back_time'] = $post['back_time'];
                    $data[$i]['model'] = $post['model'][$key];
                    $data[$i]['status'] = Product::NO_SEND;
                    $post['product_no_start'][$key] = sprintf("%0".strlen($post['product_no_start'][$key])."d", $post['product_no_start'][$key]+1);
                    $data[$i]['agent_id'] = $this->agentId;
                    $data[$i]['user_id'] = $this->agentAppUser->id;
                    $data[$i]['serial'] = $post['serial'];
                    $data[$i]['name'] = $this->agentAppUser->real_name ?: $this->agentAppUser->user_name;
                }

                $result = Product::updateAll([
                    'status' => Product::NO_SEND,
                    'send_time' => 0,
                    'user_name' => !empty($this->agentAppUser->real_name) ?: $this->agentAppUser->user_name,
                    'user_code' => $this->agentAppUser->user_code,
                    'user_id' => $this->agentAppUser->id,
                    'back_time' => $post['back_time']
                ], $condition);

                $success = Yii::$app->db->createCommand()
                    ->batchInsert(ProductLog::tableName(), [
                        'mobile',
                        'user_name',
                        'user_code',
                        'product_no',
                        'agent_product_type_id',
                        'back_time',
                        'model',
                        'status',
                        'agent_id',
                        'user_id',
                        'serial',
                        'name',
                    ], $data)
                    ->execute();

                unset($data);
            }
            if ($result == true && $success) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', '回拨成功');
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '回拨失败');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    // 查找代理商
    public function actionSearchUser()
    {
        $id = $this->request->post('id');
        $user = User::findOne([
            'id' => $id,
            'agent_id' => $this->agentId
        ]);
        if (empty($user)) {
            $data = [
                'code' => - 1,
                'msg' => '代理商不存在'
            ];
            return $this->asJson($data);
        }
        $data = [
            'code' => 1,
            'msg' => '',
            'data' => $user
        ];
        return $this->asJson($data);
    }

    public function actionSend()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::find();
            $model->select([
                'id',
                'product_type_id'
            ]);
            $model->andWhere([
                'agent_id' => $this->agentId
            ]);
            $model->with('productType');
            $model->asArray();
            $product_type = $model->all();
            $type = array();

            $user = User::find();
            $user->andWhere(['agent_id'=>$this->agentId]);
            $user->andWhere(['<>','id',$this->agentAppUser->id]);
            $user = $user->asArray()->all();
            foreach($user as $key=>$val){
                $agentUser[$val['id']] = !empty($val['real_name']) ? $val['real_name'] : $val['user_name'];
            }
            foreach ($product_type as $key => $val) {
                $type[$key]['name'] = $val['productType']['name'];
                $type[$key]['id'] = $val['productType']['id'];
            }
            $number = ProductLog::getSerial(Product::SEND);
            $data['html'] = $this->renderPartial('send', [
                'number' => $number,
                'type' => $type,
                'user' => $agentUser,
            ]);
        }
        return $this->asJson($data);
    }

    // 下发
    public function actionSendAdd()
    {
        $post = $this->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $user = User::findOne(['user_code'=>$post['user_code']]);
            if($user->register == User::NOT_REGISTER )
            {
                throw new \Exception('该代理商还没登记');
            }

            if($res = Product::existProductOrder($post['product_no_start'], $post['product_amount'])){
                throw new \Exception('下发的机具中已有交易订单，无法下发');
            }

            foreach ($post['product_no_start'] as $key => $val) {

                if(strlen($post['product_no_start'][$key]) != strlen($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号为'.$post['product_no_start'][$key].'和机具编号为'.$post['product_no_end'][$key].'长度不一样');
                }

                if(intval($post['product_no_start'][$key]) > intval($post['product_no_end'][$key]))
                {
                    throw new \Exception('机具编号'.$post['product_no_start'][$key] . '大于机具编号'.$post['product_no_end'][$key]);
                }


                $condition = [
                    'and',
                    [
                        '!=',
                        'activate_status',
                        Product::YES
                    ],
                    [
                        'between',
                        'product_no',
                        $post['product_no_start'][$key],
                        $post['product_no_end'][$key]
                    ],
                    [
                        'agent_id' => $this->agentAppUser->agent_id
                    ]
                ];
                // var_dump($condition);die;
                $result = Product::updateAll([
                    'user_name' => $post['user_name'],
                    'user_code' => $post['user_code'],
                    'user_id' => $post['user_id'],
                    'status' => Product::SEND,
                    'mobile' => $post['mobile'],
                    'send_time' => time()
                ], $condition);

                for ($i = 0; $i < $post['product_amount'][$key]; $i ++) {
                    $data[$i]['product_no'] = $post['product_no_start'][$key];
                    $data[$i]['agent_product_type_id'] = $post['agent_product_type_id'][$key];
                    $data[$i]['send_time'] = time();
                    $data[$i]['model'] = $post['model'][$key];
                    $data[$i]['status'] = Product::SEND;
                    $post['product_no_start'][$key] = sprintf("%0".strlen($post['product_no_start'][$key])."d", $post['product_no_start'][$key]+1);
                    $data[$i]['agent_id'] = $this->agentId;
                    $data[$i]['user_id'] = $this->agentAppUser->id;
                    $data[$i]['serial'] = $post['serial'];
                    $data[$i]['name'] = $this->agentAppUser->real_name ?: $this->agentAppUser->user_name;
                    $data[$i]['user_name'] = $post['user_name'];
                    $data[$i]['user_code'] = $post['user_code'];
                    $data[$i]['mobile'] = $post['mobile'];
                }

                $success = Yii::$app->db->createCommand()
                    ->batchInsert(ProductLog::tableName(), [
                        'product_no',
                        'agent_product_type_id',
                        'send_time',
                        'model',
                        'status',
                        'agent_id',
                        'user_id',
                        'serial',
                        'name',
                        'user_name',
                        'user_code',
                        'mobile'
                    ], $data)
                    ->execute();

                unset($data);
            }
            if ($result == true && $success) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', '下发成功');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }
    //开启冻结
    public function actionStart(){
        if ($this->request->isAjax) {
            try{
                $user = Yii::$app->user->identity;
                $pro = Product::findOne(['id'=>$this->request->post('id')]);
                Product::updateAll(['frost_status' => Product::FROST_START], ['id'=>$this->request->post('id'), 'agent_id'=>$user['agent_id']]);
                Yii::$app->session->setFlash('success', '冻结成功');
            }catch (\Exception $e){
                $msg = $e->getMessage();
                Yii::$app->session->setFlash('error', $msg);
            }
        }
        return $this->asJson([]);
    }

    //取消冻结
    public function actionFrozen()
    {
        if ($this->request->isAjax) {
            try{
                $user = Yii::$app->user->identity;
                Product::updateAll(['frost_status' => Product::FROST_STOP], ['id'=>$this->request->post('id'), 'agent_id'=>$user['agent_id']]);
                Yii::$app->session->setFlash('success', '取消冻结');
            }catch (\Exception $e){
                $msg = $e->getMessage();
                Yii::$app->session->setFlash('error', $msg);
            }
        }
        return $this->asJson([]);
    }

    // 基本信息
    public function actionInfo()
    {
        $id = $this->request->post('id');

        $status = Product::StatusLabels();
        $activate_status = Product::ActivateStatusLabels();


        $model = Product::find();
        $model->andWhere([
            'id' => $id
        ]);
        $model->with(['agentProductType'=>function($q){
            $q->with('productType');
        }]);
        $model->with('productUser');
        $model->asArray();
        $data['info'] = $model->one();

        if($data['info']['status'] == Product::NO_SEND){
            if($data['info']['user_id'] == $this->agentAppUser->id){
                $data['info']['status'] = Product::IN_STORE;
            }else{
                $data['info']['status'] = Product::SEND;
            }
        }

        $data['info']['ActivateStatus'] = $activate_status[$data['info']['activate_status']];
        $data['info']['user_code'] = $data['info']['productUser']['user_code'];
        $data['info']['user_name'] = $data['info']['productUser']['real_name'] ? $data['info']['productUser']['real_name'] : $data['info']['productUser']['user_name'];
        $data['info']['status_text'] = $status[$data['info']['status']];

        $productLog = ProductLog::find();
        $productLog->andWhere(['product_no'=>$data['info']['product_no']]);
        $productLog->with(['agentProductType'=>function($q){
            $q->with('productType');
        }]);
        $productLog = $productLog->all();
        foreach($productLog as $key => $val)
        {
            if($val->status == Product::IN_STORE)
            {
                $data['store'][$key]['serial'] = $val->serial;
                $data['store'][$key]['store_time'] = $val->store_time;
                $data['store'][$key]['expire_time'] = $val->expire_time;
                $data['store'][$key]['name'] = $val->name;
                $data['store'][$key]['type_name'] = $val->agentProductType->productType->name;
                $data['store'][$key]['model'] = $val->model;
            }
            if($val->status == Product::SEND)
            {
                $data['send'][$key]['serial'] = $val->serial;
                $data['send'][$key]['send_time'] = $val->send_time;
                $data['send'][$key]['name'] = $val->name;
                $data['send'][$key]['type_name'] = $val->agentProductType->productType->name;
                $data['send'][$key]['model'] = $val->model;
                $data['send'][$key]['user_name'] = $val->user_name;
                $data['send'][$key]['user_code'] = $val->user_code;
                $data['send'][$key]['mobile'] = $val->mobile;
                $data['send'][$key]['expire_time'] = $data['info']['expire_time'];
            }
            if($val->status == Product::REFUND)
            {
                $data['refund'][$key]['serial'] = $val->serial;
                $data['refund'][$key]['name'] = $val->name;
                $data['refund'][$key]['refund_time'] = $val->refund_time;
                $data['refund'][$key]['type_name'] = $val->agentProductType->productType->name;
                $data['refund'][$key]['model'] = $val->model;
            }
            if($val->status == Product::NO_SEND)
            {
                $data['back'][$key]['serial'] = $val->serial;
                $data['back'][$key]['back_time'] = $val->back_time;
                $data['back'][$key]['name'] = $val->name;
                $data['back'][$key]['type_name'] = $val->agentProductType->productType->name;
                $data['back'][$key]['user_name'] = $val->user_name;
                $data['back'][$key]['user_code'] = $val->user_code;
                $data['back'][$key]['mobile'] = $val->mobile;
                $data['back'][$key]['model'] = $val->model;
                $data['back'][$key]['expire_time'] = $data['info']['expire_time'];
            }
        }
        // var_dump($product->productLog);die;
        $data['html'] = '';
        $data['html'] = $this->renderPartial('info', [
            'product' => $data
        ]);
        return $this->asJson($data);
    }

    // 导出
    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);

        $status = Product::StatusLabels();
        $activate_status = Product::ActivateStatusLabels();

        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '机具信息';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            // var_dump($rows);die;
            Excel::export([
                'models' => $rows,
                'fileName' => '机具信息',
                'columns' => [
                    [
                        'attribute' => 'product_no',
                        'header' => '机具编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t".$models['product_no'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '机具类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['agentProductType']['productType']['name'];
                        }
                    ],
                    [
                        'attribute' => 'model',
                        'header' => '机具型号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['model'];
                        }
                    ],
                    [
                        'attribute' => 'store_time',
                        'header' => '入库日期',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d', $models['store_time']);
                        }
                    ],
                    [
                        'attribute' => 'expire_time',
                        'header' => '到期日期',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d', $models['expire_time']);
                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['productUser']['user_code'];
                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return !empty($models['productUser']['user_name']) ? $models['productUser']['user_name'] : '-';
                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '代理商姓名',
                        'format' => 'text',
                        'value' => function ($models) {
                            return !empty($models['productUser']['real_name']) ? $models['productUser']['real_name'] : '-';
                        }
                    ],
                    [
                        'attribute' => 'activate_time',
                        'header' => '激活时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['activate_time']) ? date('Y-m-d H:i:s', $models['activate_time']) : '';
                        }
                    ],
                    [
                        'attribute' => 'activate_status',
                        'header' => '激活状态',
                        'format' => 'text',
                        'value' => function ($models) use($activate_status) {
                            return $activate_status[$models['activate_status']];
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'header' => '状态',
                        'format' => 'text',
                        'value' => function ($models) use($status) {
                            return $status[$models['status']];
                        }
                    ],
                    [
                        'attribute' => 'send_time',
                        'header' => '下发时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['send_time']) ? date('Y-m-d H:i:s', $models['send_time']) : '';
                        }
                    ],
                    [
                        'attribute' => 'refund_time',
                        'header' => '退货日期',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['refund_time']) ? date('Y-m-d', $models['refund_time']) : '';
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
//        return;
    }

    protected function export($get)
    {
        $model = Product::find();
        $model->andFilterWhere([
            'model' => $get['model']
        ]);
        if($get['user_name'])
        {
            if($get['is_search_children'] == 2) {
                $model->with('productUser');
                $user = User::find();
                $user->andFilterWhere([
                    'or',
                    [
                        'like',
                        'user_name',
                        $get['user_name']
                    ],
                    [
                        'like',
                        'real_name',
                        $get['user_name']
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
                $ids_array = array_merge($ids_array, $children_ids);

                $model->andWhere([
                    'user_id' => $ids_array
                ]);
            }else{
                $model->joinWith(['productUser'=>function($q) use($get){
                    $q->andFilterWhere([
                        'or',
                        [
                            'like',
                            'user_name',
                            $get['user_name']
                        ],
                        [
                            'like',
                            'real_name',
                            $get['user_name']
                        ]
                    ]);
                }]);
            }
        }else{
            $model->with('productUser');
        }

        $model->andFilterWhere([
            'status' => $get['status']
        ]);
        $model->andFilterWhere([
            'activate_status' => $get['activate_status']
        ]);
        
        $model->andWhere([
            'agent_id' => $this->agentId
        ]);
        
        // 到期日期
       if (! empty($get['expire_time_start'])) {
            $model->andFilterWhere([
                '>=',
                'expire_time',
                strtotime($get['expire_time_start'])
            ]);
        } if (! empty($get['expire_time_end'])) {
            $model->andFilterWhere([
                '<=',
                'expire_time',
                strtotime($get['expire_time_end'] . '23:59:59')
            ]);
        }
        
        // 激活时间
        if (! empty($get['activate_time_start'])) {
            $model->andFilterWhere([
                '>=',
                'activate_time',
                strtotime($get['activate_time_start'])
            ]);
        } if (! empty($get['activate_time_end'])) {
            $model->andFilterWhere([
                '<=',
                'activate_time',
                strtotime($get['activate_time_end'] . '23:59:59')
            ]);
        }
        
        // 机型编号
        if (! empty($get['product_no_start'])) {
            $model->andFilterWhere([
                '>=',
                'product_no',
                $get['product_no_start'],
            ]);
        }
        if (! empty($get['product_no_end'])) {
            $model->andFilterWhere([
                '<=',
                'product_no',
                $get['product_no_end']
            ]);
        }
        
        // 下发时间
        if (! empty($get['send_time_start'])) {
            $model->andFilterWhere([
                '>=',
                'send_time',
                strtotime($get['send_time_start'])
            ]);
        } if (! empty($get['send_time_end'])) {
            $model->andFilterWhere([
                '<=',
                'send_time',
                strtotime($get['send_time_end'] . '23:59:59')
            ]);
        }

        $model->with(['agentProductType'=>function($q){
            $q->with('productType');
        }]);
        $count = $model->count();
        $limit = self::LIMIT;
        $model->asArray();
        for ($i = 0; $i <= $count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            yield $model->all();
        }
    }

    // 导入
    public function actionImport()
    {
        // 实例化
        $model = new Product();
        if (Yii::$app->request->isPost) {
            $file = yii\web\UploadedFile::getInstance($model, 'file'); // print_r($file);exit;
            $path = "upload/excel/" . date("Ymd", time()) . "/"; // print_r($path);exit;
            if ($file && $model->validate()) {
                if (! file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $file->saveAs($path . time() . '.' . $file->getExtension()); // print_r($file);exit;
                Yii::$app->session->setFlash('success', '导入成功！');
                $this->data_import($path . time() . '.' . $file->getExtension());
            }
        }
        return $this->asJson([]);
        // return $this->render('import',['model'=>$model]);
    }

    public function data_import($file)
    {
        $product_model = new Product();
        $product_model = $product_model->attributeLabels();
        // var_dump($product_model);die;
        $ok = '';
        $serial_store = 'RK' . date('Ymd') . rand(1000, 9999);
        $filename = $file; // print_r($filename);exit;
        $tag_data = \moonland\phpexcel\Excel::import($filename, [
            'setFirstRecordAsKeys' => true,
            'setIndexSheetByName' => true,
            'getOnlySheet' => 'sheet1'
        ]);
        
        $i = 0;
        
        $product_no = $product_model['product_no'];
        $model = $product_model['model'];
        $type = $product_model['type_id'];
        $store_time = $product_model['store_time'];
        $expire_time = $product_model['expire_time'];
        
        foreach ($tag_data as $key => $val) {
            $data[$i]['product_no'] = $val[$product_no];
            $data[$i]['type_id'] = $this->getTypeId($val[$type]);
            $data[$i]['model'] = $val[$model];
            $data[$i]['store_time'] = strtotime($val[$store_time]);
            $data[$i]['expire_time'] = strtotime($val[$expire_time]);
            $data[$i]['agent_id'] = $this->agentId;
            $data[$i]['user_id'] = $this->agentAppUser->id;
            $data[$i]['status'] = 1;
            unset($tag_data[$key]);
            $i ++;
        }
        // var_dump($data);die;
        
        $product = Yii::$app->db->createCommand()
            ->batchInsert(Product::tableName(), [
            'product_no',
            'type_id',
            'model',
            'store_time',
            'expire_time',
            'agent_id',
            'status',
        ], $data)
            ->execute();

        $arr['user_id'] = $this->agentAppUser->id;
        $arr['serial'] = $serial_store;
        $arr['name'] = $this->agentAppUser->user_name;

        array_walk($data, function (&$value, $key, $arr) {
            $value = array_merge($value, $arr);
        }, $arr);

        $result = Yii::$app->db->createCommand()
            ->batchInsert(ProductLog::tableName(), [
                'product_no',
                'agent_product_type_id',
                'model',
                'store_time',
                'expire_time',
                'agent_id',
                'status',
                'user_id',
                'serial',
                'name'
            ], $data)
            ->execute();

        if ($product && $result) {
            $ok = 1;
        }
        if ($ok == 1) {
            $this->redirect(array(
                'index'
            ));
        } else {
            Yii::$app->session->setFlash('error', '导入失败！');
        }
    }

    public function getTypeId($name)
    {
        $type = ProductType::findOne([
            'name' => $name
        ]);
        $agent_product_type = AgentProductType::findOne(['product_type_id'=>$type['id'], 'agent_id'=>$this->agentId]);
        return $agent_product_type['id'];
    }
}