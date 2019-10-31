<?php
namespace frontend\modules\sys\controllers;

use yii;
use common\models\agent\AgentRechargeLog;
use common\services\agent\AgentRechargeService;
use moonland\phpexcel\Excel;

class RechargeController extends \frontend\controllers\MController
{
    const LIMIT = 2000;

    /**
     * @actionIndex 充值管理
     * @return string
     */
    public function actionIndex()
    {
        $data = $this->agentModel;

    	return $this->render('index', ['data' => $data]);
    }

    /**
     * @actionList 充值记录列表
     */
    public function actionList()
    {

        $recharge_no = $this->request->get('recharge_no');
        $type = $this->request->get('type');
        $status = $this->request->get('status');
        $created_start = $this->request->get('created_start');
        $created_end = $this->request->get('created_end');

        $agentModel = $this->agentModel;

        $model = AgentRechargeLog::find();
        $model->andWhere([
            'agent_id' => $agentModel->id,
            'is_deleted' => AgentRechargeLog::NORMAL,
        ]);

        $model->andFilterWhere([
            'recharge_no' => $recharge_no
        ]);
        $model->andFilterWhere([
            'type' => $type
        ]);
        $model->andFilterWhere([
            'status' => $status
        ]);

        if (! empty($created_start)) {
            $model->andWhere([
                '>=',
                'created_at',
                strtotime($created_start . '00:00:00')
            ]);
        }

        if (! empty($created_end)) {
            $model->andWhere([
                '<=',
                'created_at',
                strtotime($created_end . '23:59:59')
            ]);
        }

        $model->orderBy(['created_at'=>SORT_DESC]);
        $data['total'] = $model->count();
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m->id,
                'recharge_no' => $m->recharge_no,
                'trade_no' => $m->trade_no,
                'title' => $m->title,
                'name' => $agentModel->name,
                'money' => $m->money,
                'pay_money' => $m->pay_money,
                'type' => $m->getType(),
                'o_type' => $m->type,
                'status' => $m->getStatus(),
                'o_status' => $m->status,
                'created_at' => ! empty($m->created_at) ? date('Y-m-d H:i:s', $m->created_at) : '',
                'pay_at' => ! empty($m->pay_at) ? date('Y-m-d', $m->pay_at) : '',
                'notify_at' => ! empty($m->notify_at) ? date('Y-m-d', $m->notify_at) : '',
                'close_at' => ! empty($m->close_at) ? date('Y-m-d', $m->close_at) : ''
            ];
        }
        return $this->asJson($data);

    }

    /**
     * @actionSettings 预警设置
     * @return mixed
     */
    public function actionSettings()
    {
    	$request = Yii::$app->request;
    	if ( $request->isPost ) {

    		$data = $request->post();

        	$agentModel = $this->agentModel;
        	$agentModel->warning_balance = isset($data['warning_balance']) ? round($data['warning_balance'], 2) : $agentModel->warning_balance;
        	$agentModel->warning_sms_number = isset($data['warning_sms_number']) ? (int)$data['warning_sms_number'] : $agentModel->warning_sms_number;
        	$agentModel->warning_real_name_auth_number = isset($data['warning_real_name_auth_number']) ? (int)$data['warning_real_name_auth_number'] : $agentModel->warning_real_name_auth_number;
        	$agentModel->warning_mobile = isset($data['warning_mobile']) ? $data['warning_mobile'] : $agentModel->warning_mobile;

            if ( $agentModel->save() ) {

            	// 记录日志
            	Yii::$app->services->agent->log('settings', '设置代付金/短信/实名次数预警信息');

				return $this->message("保存成功", $this->redirect(['index']));
            }
    	}

		return $this->message("保存失败", $this->redirect(['index']), 'error');
    }

    /**
     * @actionRecharge 充值
     * @return mixed
     */
    public function actionRecharge()
    {

		$request = Yii::$app->request;
    	if ( $request->isPost ) {

    		$data = $request->post();

    		$rechargeTypeList = AgentRechargeLog::typeLabels();
//    		$rechargeMoneyList = AgentRechargeLog::$money;
//    		$rechargeMoney = isset($rechargeMoneyList[$data['recharge_money']]) ? round($data['recharge_money'], 2) : 0;
    		$rechargeMoney = round($data['recharge_money'], 2);

            $agentModel = $this->agentModel;

            // 生成充值记录 返回充值单号
            $AgentRechargeService = new AgentRechargeService();
            $rechargeNo = $AgentRechargeService -> addAgentRechargeLog($agentModel->id, $data['recharge_type'], $rechargeMoney);

            if($rechargeNo){

                // 记录日志
                Yii::$app->services->agent->log(
                    'recharge',
                    '已提交充值【' . $rechargeTypeList[$data['recharge_type']] . '】' . $rechargeMoney . '元, 未入账, 单号:'.$rechargeNo
                );

                return $this->message("提交成功", $this->redirect(['index']));

            }

            return $this->message("提交失败,请联系管理员", $this->redirect(['index']), 'error');
    	}

		return $this->message("非法请求", $this->redirect(['index']), 'error');
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        if ( $request->isPost ) {

            $data = $request->post();
            if(isset($data['id']) && is_array($data['id'])){

                $agentModel = $this->agentModel;

                $res = (new AgentRechargeService()) -> deleteRechargeLog($data['id'], $agentModel->id);
                if($res){
                    return $this->message("删除成功", $this->redirect(['index']));
                }
            }
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * @actionExport 导出充值记录
     * @return mixed
     */
    public function actionExport()
    {
        $agentModel = $this->agentModel;

        $get = $this->request->get();
        $get['id'] = $agentModel->id;
        $data = $this->export($get);

        $url =  Yii::$app->request->referrer;
        ob_end_clean();//清除缓冲区,避免乱码
        $file = '商户交易记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach($data as $k => $rows)
        {
            Excel::export([
                'models' => $rows,
                'fileName' => '代理商充值记录',
                'columns' => [
                    [
                        'attribute' => 'recharge_no',
                        'header' => '订单号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['recharge_no'];
                        }
                    ],
                    [
                        'attribute' => 'name',
                        'header' => '充值人',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['id'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '充值类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            $typeLabels = AgentRechargeLog::typeLabels();
                            return $typeLabels[$models['type']];
//                            return $models['type'];
                        }
                    ],
                    [
                        'attribute' => 'money',
                        'header' => '充值金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['money'];
                        }
                    ],
                    [
                        'attribute' => 'trade_no',
                        'header' => '交易号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['trade_no'];
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'header' => '状态',
                        'format' => 'text',
                        'value' => function ($models) {
                            $statusLabels = AgentRechargeLog::statusLabels();
                            return $statusLabels[$models['status']];
//                            return $models['status'];
                        }
                    ],
                    [
                        'attribute' => 'pay_money',
                        'header' => '支付金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['pay_money'];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '交易时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                    [
                        'attribute' => 'pay_at',
                        'header' => '支付时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['pay_at']);
                        }
                    ],
                ]
            ]);
        }

        return yii::$app->util->alert('导出成功',$url);
        exit;
    }

    /**
     * @export 处理导出数据
     * @param $get
     * @return \Generator
     */
    protected function export($get)
    {

        $model = AgentRechargeLog::find();
        $model->andWhere([
            'agent_id' => $get['id']
        ]);

        $model->andFilterWhere([
            'recharge_no' => $get['recharge_no']
        ]);
        $model->andFilterWhere([
            'type' => $get['type']
        ]);
        $model->andFilterWhere([
            'status' => $get['status']
        ]);

        if (! empty($created_start)) {
            $model->andWhere([
                '>=',
                'created_at',
                strtotime($get['created_start'] . '00:00:00')
            ]);
        }

        if (! empty($created_end)) {
            $model->andWhere([
                '<=',
                'created_at',
                strtotime($get['created_end'] . '23:59:59')
            ]);
        }

        $count = $model->count();

        $model->limit(self::LIMIT);
        $limit=self::LIMIT;
        $model->asArray();
        for($i=0;$i<=$count;){
            $model->limit($limit)->offset($i);
            $i=$i+$limit;
            yield $model->all();
        }

    }
}