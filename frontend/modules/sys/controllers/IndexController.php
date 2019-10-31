<?php
namespace frontend\modules\sys\controllers;

use yii;
use common\helpers\FormHelper;
use common\models\agent\Agent;

class IndexController extends \frontend\controllers\MController
{

    public function actionIndex()
    {
        $model = $this->agentModel;
        if ($this->request->isPost) {
            $model->load([
                'min_cashback' => $this->request->post('min_cashback'),
                'cashback_fee' => $this->request->post('cashback_fee'),
                'cashback_tax_point' => $this->request->post('cashback_tax_point'),
                'cash_fee' => $this->request->post('cash_fee'),
                'min_cash_amount' => $this->request->post('min_cash_amount'),
                'tax_point' => $this->request->post('tax_point')
            ], '');
            if (! $model->save()) {
                $msg = FormHelper::multiErrors2Msg($model->errors);

                $msg = str_replace('"', '', trim($msg, '。'));
                return $this->message($msg, $this->redirect(['index']), 'error');

            } else {
                return $this->message('修改成功', $this->redirect(['index']));
            }
        }
        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionAjaxEditStatus()
    {
        if( $this->request->isAjax ){
            $status = $this->request->post('status');

            $data['cash_status'] = (int)$status;

            $res = Yii::$app->db->createCommand()->update(Agent::tableName(), $data, 'id = '.$this->agentId)->execute();
            if ( $res ) {
                return $this->asJson(['status'=>200, 'msg'=>'success']);
            } else {
                return $this->asJson(['status'=>400, 'msg'=>'fail']);
            }
        }
    }
}