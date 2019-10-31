<?php
namespace backend\modules\agent\controllers;

use common\models\user\nestedSets\UserLink;
use common\services\StoreMenuServices;
use common\models\user\User;
use yii;
use backend\modules\agent\controllers\BaseController;
use common\models\agent\Agent;
use common\helpers\FormHelper;
use common\models\agent\AgentUser;

class IndexController extends BaseController
{

    /**
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = Agent::find();
        $model->andFilterWhere([
            'like',
            'name',
            $this->request->post('name')
        ]);
        $model->andFilterWhere([
            'like',
            'contacts',
            $this->request->post('contacts')
        ]);
        $model->andFilterWhere([
            'status' => $this->request->post('status')
        ]);
        $model->andFilterWhere([
            'province_id' => $this->request->post('province_id')
        ]);
        $model->andFilterWhere([
            'city_id' => $this->request->post('city_id')
        ]);
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));
        
        $data['rows'] = [];
        $model->with('province');
        $model->with('city');
        $model->with('county');
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'name' => $m->name,
                'number' => $m->number,
                'contract_date' => $m->contract_date,
                'contacts' => $m->contacts,
                'expired_days' => number_format(($m->expired_time-time())/3600/24) > 0 ? number_format(($m->expired_time-time())/3600/24) : 0,
                'mobile' => $m->mobile,
                'mailbox' => $m->mailbox,
                'province' => $m->province->title,
                'city' => $m->city->title,
                'address' => $m->address,
                'created_at' => date('Y-m-d H:i:s', $m->created_at),
                'status' => $m->getStatus(),
                'admin_name' => !empty($m->admin_name) ?  $m->admin_name .'●' . Yii::$app->params['title']  : '',
                'agent_fee' => $m->agent_fee
            ];
        }
        return $this->asJson($data);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new Agent();
            }
            $data['html'] = $this->renderPartial('add', [
                'model' => $model
            ]);
        }
        
        return $this->asJson($data);
    }

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new Agent();
            }
            $data['html'] = $this->renderPartial('edit', [
                'model' => $model
            ]);
        }

        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        if ($this->request->isPost) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                $model = new Agent();
            }

            $agent = Agent::findOne(['number'=>$this->request->post('number')]);
            if($agent){
                Yii::$app->session->setFlash('error', '该代理商编号已存在');
                return $this->redirect(['index']);
            }

            $user = User::findOne(['mobile'=>$this->request->post('mobile')]);
            if($user){
                Yii::$app->session->setFlash('error', '该手机号已被使用过了');
                return $this->redirect(['index']);
            }

            $model->load([
                'name' => $this->request->post('name'),
                'number' => $this->request->post('number'),
                'contract_date' => $this->request->post('contract_date'),
                'county_id' => $this->request->post('county_id'),
                'contacts' => $this->request->post('contacts'),
                'mobile' => $this->request->post('mobile'),
                'mailbox' => $this->request->post('mailbox'),
                'address' => $this->request->post('address'),
                'admin_name' =>$this->request->post('admin_name')
            ], '');
            $isNewRecord = $model->isNewRecord;
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if ($model->save()) {
                
                $userLoad = [
                    'parent_id' => 0,
                    'agent_id' => $model->id
                ];
                if ($isNewRecord) {
                    $user = new User();
                    $user->setPassword('888888');
                    Yii::$app->session->setFlash('success', '添加成功');
                } else {
                    $user = User::findOne([
                        'mobile' => $model->mobile
                    ]);
                    Yii::$app->session->setFlash('success', '修改成功');
                }
                $userLoad['mobile'] = $model->mobile;
                $userLoad['user_name'] = $model->name;
                $user->load($userLoad, '');
                $user->generateAuthKey();
                if (! $user->save()) {
                    $transaction->rollBack();
                    $msg = FormHelper::multiErrors2Msg($user->errors);
                    if (! empty($msg)) {
                        Yii::$app->session->setFlash('error', $msg);
                    } else {
                        Yii::$app->session->setFlash('error', '操作失败');
                    }
                }
                if ($isNewRecord) {
                    $userLinkModel = new UserLink([
                        'user_id' => $user->id,
                        'agent_id' => $model->id
                    ]);
                    if (! $userLinkModel->makeRoot()) {
                        $transaction->rollBack();
                        $msg = FormHelper::multiErrors2Msg($userLinkModel->errors);
                        if (! empty($msg)) {
                            Yii::$app->session->setFlash('error', $msg);
                        } else {
                            Yii::$app->session->setFlash('error', '操作失败');
                        }
                    }
                }
                $transaction->commit();
            } else {
                $transaction->rollBack();
                $msg = FormHelper::multiErrors2Msg($model->errors);
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

//    修改
    public function actionEditDo()
    {
        if ($this->request->isPost) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            if (empty($model)) {
                Yii::$app->session->setFlash('error', '该代理商不存在');
                return $this->redirect(['index']);
            }

            $agent = Agent::find();
            $agent -> andWhere(['number'=>$this->request->post('number')]);
            $agent -> andWhere(['!=', 'id', $this->request->post('id')]);
            if( $agent->one() ){
                Yii::$app->session->setFlash('error', '该代理商编号已存在');
                return $this->redirect(['index']);
            }

            $mobile = $model['mobile'];

            $agentUser = User::findOne(['mobile'=>$mobile, 'agent_id'=>$this->request->post('id')]);
            $user = User::find();
            $user -> andWhere(['mobile'=>$this->request->post('mobile')]);
            $user -> andWhere(['!=', 'id', $agentUser['id']]);
            if($user->one()){
                Yii::$app->session->setFlash('error', '该手机号已被使用过了');
                return $this->redirect(['index']);
            }

            $model->load([
                'name' => $this->request->post('name'),
                'number' => $this->request->post('number'),
                'contract_date' => $this->request->post('contract_date'),
                'province_id' => $this->request->post('province_id'),
                'city_id' => $this->request->post('city_id'),
                'county_id' => $this->request->post('county_id'),
                'contacts' => $this->request->post('contacts'),
                'mobile' => $this->request->post('mobile'),
                'mailbox' => $this->request->post('mailbox'),
                'address' => $this->request->post('address'),
                'admin_name' => $this->request->post('admin_name'),
            ], '');
            $isNewRecord = $model->isNewRecord;
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if ($model->save()) {

                $userLoad = [
                    'parent_id' => 0,
                    'agent_id' => $model->id
                ];
                if ($isNewRecord) {
                    $user = new User();
                    $user->setPassword('888888');
                    Yii::$app->session->setFlash('success', '修改成功');
                } else {
                    $user = User::findOne([
                        'mobile' => $mobile
                    ]);
                    Yii::$app->session->setFlash('success', '修改成功');
                }

                $userLoad['mobile'] = $model->mobile;
                $userLoad['user_name'] = $model->name;
                $user->load($userLoad, '');
                $user->generateAuthKey();
                if (! $user->save()) {
                    $transaction->rollBack();
                    $msg = FormHelper::multiErrors2Msg($user->errors);
                    if (! empty($msg)) {
                        Yii::$app->session->setFlash('error', $msg);
                    } else {
                        Yii::$app->session->setFlash('error', '操作失败');
                    }
                }
                if ($isNewRecord) {
                    $userLinkModel = new UserLink([
                        'user_id' => $user->id,
                        'agent_id' => $model->id
                    ]);
                    if (! $userLinkModel->makeRoot()) {
                        $transaction->rollBack();
                        $msg = FormHelper::multiErrors2Msg($userLinkModel->errors);
                        if (! empty($msg)) {
                            Yii::$app->session->setFlash('error', $msg);
                        } else {
                            Yii::$app->session->setFlash('error', '操作失败');
                        }
                    }
                }
                $transaction->commit();
            } else {
                $transaction->rollBack();
                $msg = FormHelper::multiErrors2Msg($model->errors);
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

//    设置手续费
    public function actionFeeEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            $data['html'] = $this->renderPartial('fee-edit', [
                'model' => $model
            ]);
        }

        return $this->asJson($data);
    }

    public function actionFeeEditDo()
    {
        if ($this->request->isPost) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);

            $model->agent_fee = $this->request->post('agent_fee');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '修改失败');
                }
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    //    充值天数
    public function actionRechargeDays()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            $data['html'] = $this->renderPartial('recharge-days', [
                'model' => $model
            ]);
        }

        return $this->asJson($data);
    }

    public function actionRechargeDaysDo()
    {
        if ($this->request->isPost) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);

            $model->expired_time = time() + $this->request->post('days')*3600*24;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '修改失败');
                }
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionMenu()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            $storeMenuServices = new StoreMenuServices();
            
            $data['html'] = $this->renderPartial('menu', [
                'data' => $storeMenuServices->getUserTree($model),
                'model' => $model
            ]);
        }
        return $this->asJson($data);
    }

    public function actionMenuAdd()
    {
        if ($this->request->isPost) {
            $model = Agent::findOne([
                'id' => $this->request->post('id')
            ]);
            $menuIds = (array) $this->request->post('menu_ids');
            $menuIds = array_unique($menuIds);
            $model->menu_ids = json_encode($menuIds);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '修改失败');
                }
            }
            return $this->redirect([
                'index'
            ]);
        }
    }

    public function actionStart()
    {
        if ($this->request->isAjax) {
            $model = Agent::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => Agent::START
                ], '');
                $success = $m->save();
            }
            if ($success) {
                Yii::$app->session->setFlash('success', '启用成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '启用失败');
                }
            }
        }
        return $this->asJson([]);
    }

    public function actionStop()
    {
        if ($this->request->isAjax) {
            $model = Agent::find();
            $model->andWhere([
                'id' => $this->request->post('id')
            ]);
            foreach ($model->all() as $m) {
                $m->load([
                    'status' => Agent::STOP
                ], '');
                $success = $m->save();
            }
            if ($success) {
                Yii::$app->session->setFlash('success', '停用成功');
            } else {
                $msg = FormHelper::multiErrors2Msg($model->errors);
                if (! empty($msg)) {
                    Yii::$app->session->setFlash('error', $msg);
                } else {
                    Yii::$app->session->setFlash('error', '停用失败');
                }
            }
        }
        return $this->asJson([]);
    }

//    查看详情
    public function actionView()
    {
        $data['html'] = '';
        $model = Agent::find();
        $model->andWhere(['id' => $this->request->post('id')]);
        $model->with('province');
        $model->with('city');
        $model->with('county');
        $data['html'] = $this->renderPartial('view', [
            'model' => $model->one()
        ]);
        return $this->asJson($data);
//        var_dump($model->one());die;
    }
}