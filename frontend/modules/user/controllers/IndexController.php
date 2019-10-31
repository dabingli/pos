<?php
namespace frontend\modules\user\controllers;

use moonland\phpexcel\Excel;
use yii;
use common\models\user\User;
use common\helpers\FormHelper;
use common\models\user\nestedSets\UserLink;
use common\models\AgentProductType;
use common\models\user\UserSettlement;
use common\models\product\Product;

class IndexController extends \frontend\controllers\MController
{

    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = User::find();
        $model->alias('a');
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'like',
            'a.user_code',
            $this->request->post('user_code')
        ]);
        $model->andFilterWhere([
            'like',
            'a.mobile',
            $this->request->post('mobile')
        ]);
        $model->andFilterWhere([
            'like',
            'a.user_name',
            $this->request->post('user_name')
        ]);
        $model->andFilterWhere([
            'a.identity' => $this->request->post('identity')
        ]);
        if ($this->request->post('parent_mobile') || $this->request->post('parent_code') || $this->request->post('parent_user')) {
            $model->joinWith([
                'parent' => function ($q) {
                    $q->alias('b');
                    $q->andFilterWhere([
                        'like',
                        'b.mobile',
                        $this->request->post('parent_mobile')
                    ]);
                    $q->andFilterWhere([
                        'like',
                        'b.user_code',
                        $this->request->post('parent_code')

                    ]);
                    $q->andFilterWhere([
                        'or',
                        [
                            'like',
                            'b.user_name',
                            $this->request->post('parent_user')
                        ],
                        [
                            'like',
                            'b.real_name',
                            $this->request->post('parent_user')
                        ],
                    ]);
                }
            ]);
        } else {
            $model->with('parent');
        }
        $model->andFilterWhere([
            'a.identity' => $this->request->post('identity')
        ]);
        if ($this->request->post('created_start')) {
            $model->andFilterWhere([
                '>=',
                'a.created_at',
                strtotime($this->request->post('created_start') . ' 00:00:00')
            ]);
        }
        if ($this->request->post('created_end')) {
            $model->andFilterWhere([
                '<=',
                'a.created_at',
                strtotime($this->request->post('created_end') . ' 23:59:59')
            ]);
        }
        if ($this->request->post('authentication_time_start')) {
            $model->andFilterWhere([
                '>=',
                'a.authentication_time',
                strtotime($this->request->post('authentication_time_start') . ' 00:00:00')
            ]);
        }
        if ($this->request->post('authentication_time_end')) {
            $model->andFilterWhere([
                '<=',
                'a.authentication_time',
                strtotime($this->request->post('authentication_time_end') . ' 23:59:59')
            ]);
        }
        $model->andFilterWhere([
            'a.status' => $this->request->post('status')
        ]);
        $model->andFilterWhere([
            'a.register' => $this->request->post('register')
        ]);
        $model->andFilterWhere([
            'a.is_authentication' => $this->request->post('is_authentication')
        ]);
        $data['total'] = $model->count();
        $offset = $this->request->post('offset');
        $limit = $this->request->post('limit');
        $model->offset($offset)->limit($limit);
        $model->orderBy([
            'id' => SORT_DESC
        ]);
        $data['rows'] = [];
        $agentUser = $this->agentAppUser;
        foreach ($model->all() as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'user_code' => $val->user_code,
                'mobile' => $val->mobile,
                'address' => $val->address,
                'bank_card' => $val->bank_card,
                'opening_bank' => $val->opening_bank,
                'real_name' => $val->real_name,
                'user_name' => $val->user_name,
                'register' => $val->getRegister(),
                'email' => $val->email,
                'parent_id' => $val->parent_id,
                'status' => $val->getStatus(),
                'settlement' => $val->parent_id == $agentUser->id ? true : false,
                'parent_mobile' => isset($val->parent->mobile) ? $val->parent->mobile : '',
                'parent_real_name' => isset($val->parent->real_name) ? $val->parent->real_name : '',
                'parent_code' => isset($val->parent->user_code) ? $val->parent->user_code : '',
                'is_authentication' => $val->getAuthentication(),
                'identity' => $val->identity,
                'authentication_time' => $val->authentication_time ? date('Y-m-d H:i:s', $val->authentication_time) : '',
                'created_at' => date('Y-m-d H:i:s', $val->created_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionView()
    {
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = User::find();
            $model->andWhere(['id' => $this->request->post('id'),'agent_id' => $this->agentId]);
            $model->with(['userSettlementMany'=>function($q){
                $q->with(['agentProductType'=>function($query){
                    $query->with('productType');
                }]);
            }]);
            $model = $model->one();
            $data['html'] = $this->renderPartial('view', [
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionSon()
    {
        $data = [];
        if ($this->request->isAjax) {
            $model = User::find();
            $model->andWhere([
                'parent_id' => $this->request->post('parent_id'),
                'agent_id' => $this->agentId
            ]);
            $model->with(['userSettlementMany'=>function($q){
                $q->with(['agentProductType'=>function($query){
                    $query->with('productType');
                }])->asArray();
            }]);
            $data = $model->all();
            $datas = [];
            foreach ($data as $key=> $val) {
                $datas[] = [
                    'id' => $val->id,
                    'user_name' => !empty($val->real_name) ? $val->real_name : $val->user_name,
                    'user_code' => $val->user_code,
                    'mobile' => $val->mobile,
                    'is_authentication' => $val->getAuthentication(),
                    'status' => $val->getStatus(),
                    'userSettlement' => $val->userSettlementMany
                ];
            }
            return $this->asJson($datas);
        }
        return $this->asJson($data);
    }

    public function actionEditParent()
    {
        $data = [];
        if ($this->request->isAjax) {
            $model = User::findOne([
                'id' => $this->request->post('id'),
                'agent_id' => $this->agentId
            ]);
            $data['html'] = $this->renderPartial('edit-parent', [
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionEditParentDo()
    {
        $model = User::findOne([
            'user_code' => $this->request->post('parent_user_code'),
            'agent_id' => $this->agentId
        ]);
        if ($model) {
            $data['user_code'] = $model->user_code;
            $data['mobile'] = $model->mobile;
            $data['user_name'] = $model->user_name;
            $userLink = UserLink::findOne([
                'user_id' => $this->request->post('id')
            ]);
            $childrenModel = $userLink->children();
            $childrenModel->andWhere([
                'agent_id' => $this->agentId
            ]);
            $childrenModel->select([
                'user_id'
            ]);
            $children = $childrenModel->indexBy('user_id')->column();
            $children[$userLink->user_id] = $userLink->user_id;
            if (in_array($model->id, $children)) {
                Yii::$app->session->setFlash('danger', '所属关系无法向下级所属');
            } else {
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                $user = User::findOne([
                    'id' => $this->request->post('id'),
                    'agent_id' => $this->agentId
                ]);
                $user->parent_id = $model->id;
                if (! $user->save()) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '修改失败');
                }
                if (! $userLink->prependTo(UserLink::findOne([
                    'user_id' => $model->id
                ]))) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '修改失败');
                } else {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '修改成功');
                }
            }
        } else {
            Yii::$app->session->setFlash('danger', '上级不存在');
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionAccount()
    {
        $data = [];
        
        if ($this->request->isAjax) {
            $model = User::findOne([
                'user_code' => $this->request->get('parent_user_code'),
                'agent_id' => $this->agentId
            ]);
            if ($model) {
                $data['user_code'] = $model->user_code;
                $data['mobile'] = $model->mobile;
                $data['user_name'] = $model->user_name;
                $userLink = UserLink::findOne([
                    'user_id' => $this->request->get('id')
                ]);
                $childrenModel = $userLink->children();
                $childrenModel->andWhere([
                    'agent_id' => $this->agentId
                ]);
                $childrenModel->select([
                    'user_id'
                ]);
                $children = $childrenModel->indexBy('user_id')->column();
                $children[$userLink->user_id] = $userLink->user_id;
                if (in_array($model->id, $children)) {
                    $data['code'] = 0;
                    $data['mgs'] = '所属关系无法向下级所属';
                    $data['data'] = [];
                } else {
                    $data['code'] = 1;
                    $data['mgs'] = '';
                    $data['data'] = $model;
                }
            } else {
                $data['code'] = 0;
                $data['mgs'] = '上级代理不存在';
                $data['data'] = [];
            }
        }
        
        return $this->asJson($data);
    }

    public function actionSettlement()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $agentUser = $this->agentAppUser;
            
            $user = User::findOne([
                'id' => $this->request->post('id')
            ]);
            if ($user->parent_id == $agentUser->id) {
                
                $data['html'] = $this->renderPartial('settlement', [
                    'model' => $user,
                    'agentProductType' => UserSettlement::find()->with([
                        'agentProductType' => function ($q) {
                            $q->with('productType');
                        }
                    ])
                        ->andWhere([
                        'user_id' => $this->request->post('id')
                    ])
                        ->andWhere([
                        'agent_id' => $this->agentId
                    ])
                        ->all()
                ]);
            }
        }
        return $this->asJson($data);
    }

    public function actionSettlementDo()
    {
        if ($this->request->isPost) {
            $data = $this->request->post('data');
            
            if (! empty($data)) {
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                $datas = [];
                $agentUser = $this->agentAppUser;
                $user = User::findOne([
                    'id' => $this->request->post('id'),
                    'agent_id' => $this->agentId
                ]);
                if ($user->parent_id != $agentUser->id) {
                    $transaction->rollBack();
                    return $this->redirect($this->message('该用户不属于二级代理', $this->redirect([
                        'index'
                    ]), 'warning'));
                }
                foreach ($data as $k => $val) {
                    $model = UserSettlement::findOne([
                        'agent_product_type_id' => $k,
                        'user_id' => $this->request->post('id')
                    ]);
                    $model->load([
                        'level_cc_settlement' => $val['level_cc_settlement'],
                        'level_dc_settlement' => $val['level_dc_settlement'],
                        'capping' => $val['capping'],
                        'cash_money' => $val['cash_money']
                    ], '');
                    if (! $model->save()) {
                        $transaction->rollBack();
                        $msgText = $this->multiErrors2Msg($model->errors);
                        return $this->redirect($this->message($msgText, $this->redirect([
                            'index'
                        ]), 'warning'));
                    }
                }
            }
        }
        
        Yii::$app->session->setFlash('success', '修改成功');
        $transaction->commit();
        return $this->redirect([
            'index'
        ]);
    }

    // 代理商信息导出
    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);
        
        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '代理商信息';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            // var_dump($rows);die;
            Excel::export([
                'models' => $rows,
                'fileName' => '代理商信息',
                'columns' => [
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_code'];
                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_name'];
                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '代理商姓名',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['real_name'];
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
                        'attribute' => 'identity',
                        'header' => '身份证号码',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['identity'];
                        }
                    ],
                    [
                        'attribute' => 'bank_card',
                        'header' => '银行卡号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['bank_card'];
                        }
                    ],
                    [
                        'attribute' => 'opening_bank',
                        'header' => '开户行',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['opening_bank'];
                        }
                    ],
                    [
                        'attribute' => 'user_name',
                        'header' => '机构名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_name'];
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'header' => '邮箱',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['email'];
                        }
                    ],
                    [
                        'attribute' => 'address',
                        'header' => '联系地址',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['address'];
                        }
                    ],
                    [
                        'attribute' => '',
                        'header' => '上级代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['parent']['real_name']) ? $models['parent']['real_name'] : ! empty($models['parent']['user_name']) ? $models['parent']['user_name'] : '-';
                        }
                    ],
                    [
                        'attribute' => 'parent_mobile',
                        'header' => '上级手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['parent']['mobile']) ? $models['parent']['mobile'] : '-';
                        }
                    ],
                    [
                        'attribute' => 'parent_user_code',
                        'header' => '上级代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['parent']['user_code']) ? $models['parent']['user_code'] : '-';
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '注册时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'is_authentication',
                        'header' => '是否实名',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getAuthentication($models['is_authentication']);
                        }
                    ],
                    [
                        'attribute' => 'authentication_time',
                        'header' => '实名时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return ! empty($models['authentication_time']) ? date('Y-m-d H:i:s', $models['authentication_time']) : '';
                        }
                    ],
                    [
                        'attribute' => '',
                        'header' => '实名证件',
                        'format' => 'text',
                        'value' => function ($models) {
                            return '';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'header' => '状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getStatus($models['status']);
                        }
                    ],
                    [
                        'attribute' => 'register',
                        'header' => '登记状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $this->getRegister($models['register']);
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
    }

    protected function export($get)
    {
        $model = User::find();
        $model->alias('a');
        $model->andWhere([
            'a.agent_id' => $this->agentId
        ]);
        $model->andFilterWhere([
            'a.user_code' => $get['user_code']
        ]);
        $model->andFilterWhere([
            'like',
            'a.mobile',
            $get['mobile']
        ]);
        $model->andFilterWhere([
            'like',
            'a.user_name',
            $get['user_name']
        ]);
        $model->andFilterWhere([
            'a.identity' => $get['identity']
        ]);
//        if ($get['parent_mobile'] || $get['parent_code']) {
            $model->joinWith([
                'parent' => function ($q) use ($get) {
                    $q->alias('b');
                    $q->andFilterWhere([
                        'like',
                        'b.mobile',
                        $get['parent_mobile']
                    ]);
                    $q->andFilterWhere([
                        'b.user_code' => $get['parent_code']
                    ]);
                    $q->andFilterWhere([
                        'like',
                        'b.user_name',
                        $get['parent_user']
                    ]);
                }
            ]);
//        }
        $model->andFilterWhere([
            'a.identity' => $get['identity']
        ]);
        if ($get['created_start']) {
            $model->andFilterWhere([
                '>=',
                'a.created_at',
                $get['created_start']
            ]);
        }
        if ($get['created_end']) {
            $model->andFilterWhere([
                '<=',
                'a.created_at',
                $get['created_start']
            ]);
        }
        if ($get['authentication_time_start']) {
            $model->andFilterWhere([
                '>=',
                'a.authentication_time',
                $get['authentication_time_start']
            ]);
        }
        if ($get['authentication_time_end']) {
            $model->andFilterWhere([
                '<=',
                'a.authentication_time',
                $get['authentication_time_end']
            ]);
        }
        $model->andFilterWhere([
            'a.status' => $get['status']
        ]);
        $model->andFilterWhere([
            'a.register' => $get['register']
        ]);
        $model->andFilterWhere([
            'a.is_authentication' => $get['is_authentication']
        ]);
        
        $count = $model->count();
        $limit = self::LIMIT;
        $model->asArray();
        for ($i = 0; $i <= $count;) {
            $model->limit($limit)->offset($i);
            $i = $i + $limit;
            yield $model->all();
        }
    }

    // 启用
    public function actionStart()
    {
        if ($this->request->isAjax) {
            $model = User::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            // $model->load([
            // 'status' => User::OPEND_STATUS
            // ], '');
            // $model->status = 1;
            // $model->save();
            // var_dump($model->getFirstErrors());die;
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => User::OPEND_STATUS
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '启用成功');
        }
        
        return $this->asJson([]);
    }

    // 停用
    public function actionStop()
    {
        if ($this->request->isAjax) {
            $model = User::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => User::CLOSE_STATUS
                ], '');
                $m->save();
            }
            Yii::$app->session->setFlash('success', '禁用成功');
        }
        
        return $this->asJson([]);
    }

    // 代理商下级用户导出
    public function actionDownload()
    {
        $id = $this->request->get('id');
        $data = $this->download($id);
        
        $url = Yii::$app->request->referrer;
        ob_end_clean(); // 清除缓冲区,避免乱码
        $file = '代理商下级用户信息';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        foreach ($data as $k => $rows) {
            // var_dump($rows);die;
            Excel::export([
                'models' => $rows,
                'fileName' => '代理商下级用户信息',
                'columns' => [
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商',
                        'format' => 'text',
                        'value' => function ($models) {
                            return isset($models['user_name']) ? $models['user_name'] : ($models['user']['user_name'] ? $models['user']['user_name'] : '');
                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return isset($models['user_code']) ? $models['user_code'] : ($models['user']['user_code'] ? $models['user']['user_code'] : '');
                        }
                    ],
                    [
                        'attribute' => 'mobile',
                        'header' => '手机号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return isset($models['mobile']) ? $models['mobile'] : ($models['user']['mobile'] ? $models['user']['mobile'] : '');
                        }
                    ],
                    [
                        'attribute' => 'is_authentication',
                        'header' => '是否实名',
                        'format' => 'text',
                        'value' => function ($models) {
                            return isset($models['Authentication']) ? $models['Authentication'] : $this->getAuthentication($models['user']['is_authentication']);
                        }
                    ],
                    [
                        'attribute' => '',
                        'header' => '结算价',
                        'format' => 'text',
                        'value' => function ($models) {
                            $userSett = isset($models['userSettlementMany']) ? $models['userSettlementMany'] : $models->user->userSettlementMany;
                            $str = '';
                            if(!empty($userSett)){
                                foreach ($userSett as $key=>$val) {
                                    $str .= $val['agentProductType']['productType']['name'] . '/' . $val['level_cc_settlement'] . '/' . $val['level_dc_settlement']
                                    . '('. $val['capping'] .')'  . '/' . $val['cash_money'] ."\r\n";
                                }
                            }
                            return $str;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'header' => '状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            return isset($models['Status']) ? $models['Status'] : $this->getStatus($models['user']['status']);
                        }
                    ]
                ]
            ]);
        }
        return yii::$app->util->alert('导出成功', $url);
        exit();
    }

    protected function download($id)
    {
        $model = User::find();
        $model->andWhere([
            'id' => $id
        ]);
        $model->with(['userSettlementMany'=>function($q){
            $q->with(['agentProductType'=>function($query){
                $query->with('productType');
            }])->asArray();
        }]);
        $user = $model->all();
        
        $userLink = UserLink::findOne([
            'user_id' => $id
        ]);
        $childrenModel = $userLink->children();
        $childrenModel->with(['user'=>function($m){
            $m->with(['userSettlementMany'=>function($q){
                $q->with(['agentProductType'=>function($query){
                    $query->with('productType');
                }]);
            }]);
        }]);

        $childrenModel->andWhere([
            'agent_id' => $this->agentId
        ]);
        $children = $childrenModel->all();
        
        yield array_merge($children, $user);
    }

    // 是否实名
    protected function getAuthentication($is_authentication)
    {
        if ($is_authentication == User::AUTH_NOT) {
            return '否';
        }
        if ($is_authentication == User::AUTH_YES) {
            return '是';
        }
        if ($is_authentication == User::SUBMISSION) {
            return '等待审核';
        }
    }

    // 用户状态
    protected function getStatus($status)
    {
        if ($status == User::OPEND_STATUS) {
            return '正常';
        } else {
            return '禁用';
        }
    }

    // 登记状态
    protected function getRegister($register)
    {
        if ($register == User::REGISTER) {
            return '已登记';
        } else {
            return '未登记';
        }
    }

    public function actionSettlementSystem()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $agentUser = $this->agentAppUser;
            
            $user = User::findOne([
                'id' => $this->request->post('id')
            ]);
            if ($user->parent_id == $agentUser->id) {
                $userSettlement = UserSettlement::find();
                $userSettlement->andWhere([
                    'user_id' => $user->id,
                    'agent_id' => $this->agentId
                ])
                    ->indexBy('agent_product_type_id')
                    ->select([
                    'agent_product_type_id'
                ]);
                $agentProductType = AgentProductType::find()->andWhere([
                    'not in',
                    'id',
                    $userSettlement->column()
                ]);
                $agentProductType->indexBy('id');
                $agentProductType->andWhere([
                    'agent_id' => $this->agentId
                ])->with('productType');
                $data['html'] = $this->renderPartial('settlement-system', [
                    'model' => $user,
                    'agentProductType' => $agentProductType->all()
                ]);
            }
        }
        return $this->asJson($data);
    }

    public function actionSettlementSystemDo()
    {
        if ($this->request->isPost) {
            $agentUser = $this->agentAppUser;
            
            $user = User::findOne([
                'id' => $this->request->post('id')
            ]);
            if ($user->parent_id == $agentUser->id) {
                $userSettlement = new UserSettlement();
                $userSettlement->load([
                    'user_id' => $user->id,
                    'agent_product_type_id' => $this->request->post('agent_product_type_id'),
                    'level_cc_settlement' => $this->request->post('level_cc_settlement'),
                    'level_dc_settlement' => $this->request->post('level_dc_settlement'),
                    'capping' => $this->request->post('capping'),
                    'cash_money' => $this->request->post('cash_money'),
                    'agent_id' => $this->agentId
                ], '');
                if ($userSettlement->save()) {
                    Yii::$app->session->setFlash('success', '新增成功');
                } else {
                    $msg = FormHelper::multiErrors2Msg($userSettlement->errors);
                    if (! empty($msg)) {
                        return $this->message($this->analyErr($userSettlement->getFirstErrors()), $this->redirect([
                            'index'
                        ]), 'error');
                    }
                }
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionFrozen()
    {
        $data['html'] = '';
        if ($this->request->isPost) {
            $model = User::find();
            $model->andWhere([
                'id' => $this->request->post('id'),
                'agent_id' => $this->agentId
            ]);
            $data['html'] = $this->renderPartial('frozen', [
                'model' => $model->all()
            ]);
        }
        return $this->asJson($data);
    }

    public function actionFrozenDo()
    {
        $data = $this->request->post('data');
        $ids = $this->request->post('ids');
        foreach ($ids as $id) {
            if (! isset($data[$id])) {
                $data[$id] = [
                    'frozen_earnings' => User::NOT_FROZEN_EARNINGS,
                    'frozen_distributing' => User::NOT_FROZEN_DISTRIBUTING
                ];
            }
        }
        foreach ($data as $k => $m) {
            $user = User::findOne([
                'id' => $k,
                'agent_id' => $this->agentId
            ]);
            
            if ($k == $this->agentAppUser->id) {
                continue;
            }
            if (isset($m['frozen_earnings'])) {
                $user->frozen_earnings = $m['frozen_earnings'];
            } else {
                $user->frozen_earnings = User::NOT_FROZEN_EARNINGS;
            }
            if (isset($m['frozen_distributing'])) {
                $user->frozen_distributing = $m['frozen_distributing'];
            } else {
                $user->frozen_distributing = User::NOT_FROZEN_DISTRIBUTING;
            }
            $user->save();
        }
        return $this->message('设置成功', $this->redirect([
            'index'
        ]), 'success');
    }
}