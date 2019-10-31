<?php
namespace frontend\modules\product\controllers;

use yii;
use common\models\AgentProductType;
use common\models\product\ProductType;
use common\helpers\FormHelper;
use common\models\user\UserSettlement;
use common\services\frontend\UserSettlementForm;

class IndexController extends \frontend\controllers\MController
{

    public function actionIndex()
    {
        $arr = ProductType::find()->indexBy('id')
            ->select([
            'name'
        ])
            ->column();
        
        return $this->render('index', [
            'selectData' => $arr
        ]);
    }

    public function actionList()
    {
        $model = AgentProductType::find();
        $model->andFilterWhere([
            'product_type_id' => $this->request->post('product_type_id')
        ]);
        $model->andWhere([
            'agent_id' => $this->agentId
        ]);
        $model->with('productType');
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        
        $data['rows'] = [];
        
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'product_type' => isset($m->productType->name) ? $m->productType->name : '',
                'level_cc_settlement' => $m->level_cc_settlement,
                'level_dc_settlement' => $m->level_dc_settlement,
                'cash_money' => $m->cash_money,
                'frozen_money' => $m->frozen_money,
                'update_user' => $m->update_user,
                'capping' => $m->capping,
                'add_user' => $m->productType->add_user,
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'updated_at' => date('Y-m-d H:i:s', $m->updated_at)
            ];
        }
        return $this->asJson($data);
    }

    public function actionAdd()
    {
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new AgentProductType();
            }
            $selectData = ProductType::find()->indexBy('id')
                ->select([
                'name'
            ])
                ->andWhere([
                'not in',
                'id',
                AgentProductType::find()->andWhere([
                    'agent_id' => $this->agentId
                ])
                    ->select([
                    'product_type_id'
                ])
                    ->indexBy('product_type_id')
                    ->column()
            ])
                ->column();
            $data['html'] = $this->renderPartial('add', [
                'model' => $model,
                'type' => $selectData
            ]);
        }
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        if ($this->request->isPost) {
            $model = AgentProductType::findOne([
                'id' => $this->request->post('id'),
                'agent_id' => $this->agentId
            ]);
            $load = [
                'agent_id' => $this->agentId,
                'level_cc_settlement' => $this->request->post('level_cc_settlement'),
                'level_dc_settlement' => $this->request->post('level_dc_settlement'),
                'capping' => $this->request->post('capping'),
                'cash_money' => $this->request->post('cash_money'),
                'frozen_money' => $this->request->post('frozen_money'),
                'update_user' => Yii::$app->user->identity->account
            ];
            if (empty($model)) {
                $model = new AgentProductType();
                $load['add_user'] = Yii::$app->user->identity->account;
                $load['product_type_id'] = $this->request->post('product_type_id');
            }
            
            $model->load($load, '');
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if (! $model->save()) {
                $transaction->rollBack();
                $msg = $this->multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    $this->message($msg, '', 'error');
                } else {
                    $this->message($msg, '操作失败', 'error');
                }
                return $this->redirect([
                    'index'
                ]);
            }
            $model->validate();
            $isNewRecord = $model->isNewRecord;
            $agentAppUser = $this->agentAppUser;
            
            $userSettlement = UserSettlementForm::findOne([
                'user_id' => $agentAppUser->id,
                'agent_id' => $this->agentId,
                'agent_product_type_id' => $model->id
            ]);
            if (empty($userSettlement)) {
                $userSettlement = new UserSettlementForm();
            }
            $userSettlement->load([
                'user_id' => $agentAppUser->id,
                'agent_id' => $this->agentId,
                'agent_product_type_id' => $model->id,
                'level_cc_settlement' => $this->request->post('level_cc_settlement'),
                'level_dc_settlement' => $this->request->post('level_dc_settlement'),
                'capping' => $this->request->post('capping'),
                'cash_money' => $this->request->post('cash_money')
            ], '');
            
            if ($userSettlement->save()) {
                if ($isNewRecord) {
                    Yii::$app->session->setFlash('success', '添加成功');
                } else {
                    Yii::$app->session->setFlash('success', '修改成功');
                }
                $transaction->commit();
            } else {
                $transaction->rollBack();
                $msg = FormHelper::multiErrors2Msg($userSettlement->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '操作失败');
                }
            }
        }
        
        return $this->redirect([
            'index'
        ]);
    }

    public function actionRewards()
    {
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = AgentProductType::findOne([
                'id' => $this->request->post('id'),
                'agent_id' => $this->agentId
            ]);
            
            $data['html'] = $this->renderPartial('rewards', [
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionRewardsDo()
    {
        if ($this->request->isPost) {
            $model = AgentProductType::findOne([
                'id' => $this->request->post('id'),
                'agent_id' => $this->agentId
            ]);
            
            if (! empty($model)) {
                
                $load = [
                    'return_days' => $this->request->post('return_days'),
                    'return_order_total_money' => $this->request->post('return_order_total_money'),
                    'return_rewards_money' => $this->request->post('return_rewards_money')
                ];
                
                $model->load($load, '');
                if (! $model->save()) {
                    Yii::$app->session->setFlash('error', '设置失败');
                }
                Yii::$app->session->setFlash('success', '设置成功');
            } else {
                
                Yii::$app->session->setFlash('error', '代理商机具类型不存在');
            }
        }
        
        return $this->redirect([
            'index'
        ]);
    }
}