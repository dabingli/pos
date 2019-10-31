<?php
namespace frontend\modules\user\controllers;

use common\helpers\FormHelper;
use common\models\user\UserIdentityAuditLog;
use yii;

class AuthenticationLogController extends \frontend\controllers\MController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = UserIdentityAuditLog::find();
        $model->alias('l');
        $model->andWhere(['l.agent_id'=>$this->agentId]);
        $model->andFilterWhere(['l.status' => $this->request->post('status')]);

        if ($this->request->post('audit_at_start')) {
            $model->andFilterWhere([
                '>=',
                'l.created_at',
                strtotime($this->request->post('audit_at_start') . ' 00:00:00')
            ]);
        }
        if ($this->request->post('audit_at_end')) {
            $model->andFilterWhere([
                '<=',
                'l.created_at',
                strtotime($this->request->post('audit_at_end') . ' 23:59:59')
            ]);
        }

       /* $model->joinWith(['user' => function($q){
            $q->select(['id','agent_id','user_code','user_name']);
//            $q->andFilterWhere(['mobile' => $this->request->post('mobile')]);
            $q->andFilterWhere(['user_code' => $this->request->post('user_code')]);

        }]);*/

        $model->leftJoin('user u', 'u.id=l.user_id');
        $model->andFilterWhere(['like', 'u.user_code', $this->request->post('user_code')]);


        $data['total'] = $model->count();
        $offset = $this->request->post('offset');
        $limit = $this->request->post('limit');
        $model->offset($offset)->limit($limit);
        $model->orderBy('l.created_at DESC');

        foreach ($model->all() as $val) {
            $data['rows'][] = [
                'id' => $val->id,
                'user_code' => $val['user']['user_code'],
                'real_name' => $val->real_name,
                'identity_card' => $val->identity_card,
                'type' => $val->getType(),
                'cardNo' => $val->cardNo,
                'order_sn' => $val->order_sn,
                'status' => $val->getStatus(),
                'description' => $val->description,
                'mobile' => $val->mobile,
                'created_at' => date('Y-m-d H:i:s',$val->created_at)
            ];
        }
        return $this->asJson($data);
    }
}