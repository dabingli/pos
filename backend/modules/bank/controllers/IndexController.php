<?php
namespace backend\modules\bank\controllers;

use yii;
use backend\modules\bank\controllers\BaseController;
use common\models\user\BankCard;

class IndexController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = BankCard::find();
        $model->andFilterWhere([
            'like',
            'name',
            $this->request->post('name')
        ]);
        $limit = $this->request->post('limit');
        $offset = $this->request->post('offset');
        $model->limit($limit);
        $model->offset($offset);
        $model->orderBy([
            'order' => SORT_ASC,
            'updated_at' => SORT_DESC
        ]);
        $data['total'] = $model->count();
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'bank' => $m->bank,
                'name' => $m->name,
                'logo' => $m->logo,
                'o_status' => $m->status,
                'status' => $m->getStatus(),
                'order' => $m->order
            ];
        }
        return $this->asJson($data);
    }

    function actionAjaxEdit()
    {
        $model = BankCard::findOne([
            'bank' => $this->request->post('pk')
        ]);
        $name = $this->request->post('name');
        $value = $this->request->post('value');
        $model->$name = $value;
        return $this->asJson([
            $model->save()
        ]);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('add');
        }

        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        $request = Yii::$app->request;
        if ( $request->isPost ) {

            $data = $request->post();

            $bankCardModel = new BankCard();
            $bankInfo = $bankCardModel->findOne(['bank'=>$data['bank']]);
            if(empty($bankInfo)){
                $bankCardModel->load([
                    'bank' => $data['bank'],
                    'name' => $data['name'],
                    'logo' => $data['logo'],
                    'order' => $data['order'],
                    'status' => $data['status'],
                ], '');
                if($bankCardModel->save()){
                    return $this->message("操作成功", $this->redirect(['index']));
                }

            }else{
                return $this->message("该银行简称已存在，请重新添加！", $this->redirect(['index']), 'error');

            }
        }

        return $this->message("操作失败！", $this->redirect(['index']), 'error');
    }

    function actionAjaxEditStatus()
    {
        $request = Yii::$app->request;
        if ( $request->isPost ) {

            $data = $request->post();
            if(isset($data['pk']) && is_array($data['pk'])){

                $pks = '';
                foreach ($data['pk'] as $k=>$v) {
                    $pks .= $k==0 ? "'{$v}'" : ",'{$v}'";
                }

                // 批量修改银行状态
                $sql = 'UPDATE ' . BankCard::tableName() . ' SET ';
                $sql .= " status = {$data['status']}";
                $sql .= " WHERE bank in ({$pks}) ";
                $sql .= " AND status != {$data['status']}";

                $res = Yii::$app->db->createCommand($sql)->execute();

                if($res){
                    return $this->message("操作成功", $this->redirect(['index']));
                }
            }
        }

        return $this->message("操作失败", $this->redirect(['index']), 'error');

    }
}