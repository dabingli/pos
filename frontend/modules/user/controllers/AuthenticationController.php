<?php
namespace frontend\modules\user\controllers;

use common\helpers\FormHelper;
use common\models\user\User;
use common\models\user\UserBankCard;
use common\models\user\UserIdentityAudit;
use common\models\user\UserIdentityAuditLog;
use yii;

class AuthenticationController extends \frontend\controllers\MController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    //    实名认证
    public function actionList()
    {
        $model = UserIdentityAudit::find();
        $model->alias('a');
        $model->andWhere(['a.agent_id'=>$this->agentId]);
        $model->andFilterWhere(['a.audit_name'=>$this->request->post('audit_name')]);
        $model->andFilterWhere(['a.status' => $this->request->post('status')]);

        if ($this->request->post('audit_at_start')) {
            $model->andFilterWhere([
                '>=',
                'a.audit_at',
                strtotime($this->request->post('audit_at_start') .' 00:00:00')
            ]);
        }
        if ($this->request->post('audit_at_end')) {
            $model->andFilterWhere([
                '<=',
                'a.audit_at',
                strtotime($this->request->post('audit_at_end') . ' 23:59:59')
            ]);
        }

        /*$model->with(['user' => function($q){
            $q->select(['id','agent_id','user_code','user_name']);
            $q->andFilterWhere(['user_code' => $this->request->post('user_code')]);
            $q->andFilterWhere(['mobile' => $this->request->post('mobile')]);
        }]);*/

        $model->leftJoin('user u', 'u.id=a.user_id');
        $model->andFilterWhere([
            'like',
            'u.mobile',
            $this->request->post('mobile')
        ]);
        $model->andFilterWhere(['like','u.user_code', $this->request->post('user_code')]);

        $data['total'] = $model->count();
        $offset = $this->request->post('offset');
        $limit = $this->request->post('limit');
        $model->offset($offset)->limit($limit);
        $model->orderBy('a.created_at DESC');

        foreach ($model->all() as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'user_code' => $val['user']['user_code'],
                'mobile' => $val['user']['mobile'],
                'real_name' => $val->real_name,
                'identity_card' => $val->identity_card,
                'type' => $val->getType(),
                'cardNo' => $val->cardNo,
                'status' => $val->getStatus(),
                'audit_name' => $val->audit_name,
                'audit_at' => !empty($val->audit_at) ? date('Y-m-d H:i:s',$val->audit_at) : '',
                'description' => $val->description,
                'created_at' => date('Y-m-d H:i:s',$val->created_at),
            ];
        }
        return $this->asJson($data);
    }

    public function actionView(){
        $data = [];
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = UserIdentityAudit::find();
            $model->andWhere([ 'id' => $this->request->post('id')]);
            $model->with(['user'=>function($q){
                $q->select(['id','mobile','user_code']);
            }]);
            $data  = $model->asArray()->one();
            $data['image'] = json_decode($data['image'],true);
            $data['html'] = $this->renderPartial('view', [
                'model' => $data
            ]);
        }
        return $this->asJson($data);
    }

    public function actionHandle(){
        $id = $this->request->post('id');
        $status = $this->request->post('status');
        $model = UserIdentityAudit::findOne(['id'=>$id]);
        $data = [
            'status' => $status,
            'audit_name' => $this->agentAppUser->user_name,
            'audit_at' => time()
        ];
        $result = Yii::$app->db->createCommand()->update(UserIdentityAudit::tableName(),$data,['id'=>$id])->execute();
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        if($result)
        {
            $user = User::findOne(['id'=>$model->user_id]);
            if($status == UserIdentityAudit::PASS){

                $userBankCard = UserBankCard::findOne(['user_id'=>$model->user_id]);
                $bank = '';
                if($userBankCard){
                    $bank = $userBankCard->bankCard->name;
                }

                $user->authentication_time = time();
                $user->real_name = $model->real_name;
                $user->bank_card = $model->cardNo;
                $user->opening_bank = $bank;
                $user->identity = $model->identity_card;
                $user->is_authentication = User::AUTH_YES;
            }else{
                $user->is_authentication = User::AUTH_NOT;
            }
            $user->save();
            $transaction->commit();
            Yii::$app->session->setFlash('success', '审核成功');
        }else {
            $transaction->rollBack();
            $msg = FormHelper::multiErrors2Msg($model->errors);
            if (! empty($msg)) {
                return $this->message($this->analyErr($model->getFirstErrors()), $this->redirect([
                    'index'
                ]), 'error');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }
}