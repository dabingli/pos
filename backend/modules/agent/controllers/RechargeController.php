<?php
namespace backend\modules\agent\controllers;

use yii;
use common\models\agent\Agent;
use common\models\agent\AgentRechargeLog;
use common\services\agent\AgentRechargeService;
use moonland\phpexcel\Excel;

class RechargeController extends BaseController
{
    const LIMIT = 2000;

    /**
     * @充值管理
     * @return string
     */
    public function actionIndex()
    {
        $agentId = $this->request->get('agent_id');
        $agentModel = new Agent();
        $agent = $agentModel->findOne(['id'=>$agentId]);
        $typeList = AgentRechargeLog::typeLabels();

        $data = [
            'agentInfo' => $agent,
            'typeList' => $typeList,
        ];

        return $this->render('index', $data);
    }

    /**
     * @充值记录列表
     */
    public function actionList()
    {
        $real_name = $this->request->get('real_name');
        $agent_phone = $this->request->get('agent_phone');
        $agent_number = $this->request->get('agent_number');
        $recharge_no = $this->request->get('recharge_no');
        $type = $this->request->get('type');
        $status = $this->request->get('status');
        $created_start = $this->request->get('created_start');
        $created_end = $this->request->get('created_end');

        $model = AgentRechargeLog::find();
        $model->select('agent_recharge_log.*, agent.name, agent.number, agent.mobile, user.real_name, user.user_name');
        $model->leftJoin('agent', 'agent.id = agent_recharge_log.agent_id');
        $model->leftJoin('user', 'agent.id = user.agent_id and user.mobile = agent.mobile');

        $model->andFilterWhere([
            'like',
            'user.real_name',
            $real_name
        ]);

        $model->andFilterWhere([
            'agent.mobile' => $agent_phone
        ]);

        $model->andFilterWhere([
            'like',
            'agent.number',
            $agent_number
        ]);

        $model->andFilterWhere([
            'like',
            'agent_recharge_log.recharge_no',
            $recharge_no
        ]);
        $model->andFilterWhere([
            'agent_recharge_log.type' => $type
        ]);
        $model->andFilterWhere([
            'agent_recharge_log.status' => $status
        ]);

        if (! empty($created_start)) {
            $model->andWhere([
                '>=',
                'agent_recharge_log.created_at',
                strtotime($created_start . '00:00:00')
            ]);
        }

        if (! empty($created_end)) {
            $model->andWhere([
                '<=',
                'agent_recharge_log.created_at',
                strtotime($created_end . '23:59:59')
            ]);
        }

        $data['total'] = $model->count();
        $model->limit($this->request->get('limit'));
        $model->offset($this->request->get('offset'));

        $model->orderBy('created_at desc');
        $model->asArray();

        $statusLabels = AgentRechargeLog::statusLabels();
        $typeLabels = AgentRechargeLog::typeLabels();

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m['id'],
                'recharge_no' => $m['recharge_no'],
                'trade_no' => $m['trade_no'],
                'title' => $m['title'],
                'real_name' => $m['real_name'] ?: $m['user_name'],
                'number' => $m['number'],
                'mobile' => $m['mobile'],
                'money' => $m['money'],
                'pay_money' => $m['pay_money'],
                'o_type' => $m['type'],
                'type' => $typeLabels[$m['type']],
                'o_status' => $m['status'],
                'status' => $statusLabels[$m['status']],
                'created_at' => ! empty($m['created_at']) ? date('Y-m-d H:i:s', $m['created_at']) : '',
                'pay_at' => ! empty($m['pay_at']) ? date('Y-m-d H:i:s', $m['pay_at']) : '',
                'notify_at' => ! empty($m['notify_at']) ? date('Y-m-d', $m['notify_at']) : '',
                'close_at' => ! empty($m['close_at']) ? date('Y-m-d', $m['close_at']) : '',
                'audit_name' => $m['audit_name'],
            ];
        }
        return $this->asJson($data);

    }

    /**
     * @充值记录详情
     * @return string
     */
    public function actionView()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $id = $this->request->post('id');
            $agentRechargeLogModel = new AgentRechargeLog;
            $rechargeLog = $agentRechargeLogModel -> findOne(['id'=>$id]);

            $agentModel = new Agent();
            $agent = $agentModel->findOne(['id'=>$rechargeLog['agent_id']]);

            $log = [
                'rechargeLog' => $rechargeLog,
                'agent' => $agent
            ];

            $data['html'] = $this->renderPartial('view', $log);
        }

        return $this->asJson($data);

    }

    /**
     * @actionPaySuccess 支付成功
     * @return mixed
     */
    public function actionPaySuccess()
    {

        $request = Yii::$app->request;
        if ($request->isPost) {

            $post = $request->post();

            if(isset($post['recharge_no'])){

                $agentRechargeService = new AgentRechargeService();
                $res = $agentRechargeService -> paySuccess($post['recharge_no']);

                if($res){

                    // 记录行为日志
                    Yii::$app->services->sys->log('pay-success', '代理商充值成功，订单号:'.$post['recharge_no']);

                    return $this->message("操作成功", $this->redirect(['index']));
                }
            }

            return $this->message("操作失败", $this->redirect(['index']), 'error');

        }

        return $this->message("非法请求", $this->redirect(['index']), 'error');
    }

    /**
     * @actionPayClose 关闭订单操作
     * @return mixed
     */
    public function actionPayClose()
    {

        $request = Yii::$app->request;
        if ($request->isPost) {

            $post = $request->post();

            if(isset($post['recharge_no'])){

                $agentRechargeService = new AgentRechargeService();
                $res = $agentRechargeService -> payClose($post['recharge_no']);

                if($res){

                    // 记录行为日志
                    Yii::$app->services->sys->log('pay-close', '代理商充值记录已关闭，订单号:'.$post['recharge_no']);

                    return $this->message("操作成功", $this->redirect(['index']));
                }
            }

            return $this->message("操作失败", $this->redirect(['index']), 'error');

        }

        return $this->message("非法请求", $this->redirect(['index']), 'error');
    }

    public function actionExport()
    {
        $get = $this->request->get();
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
                        'attribute' => 'real_name',
                        'header' => '代理商名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['real_name'];
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'header' => '充值类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            $typeLabels = AgentRechargeLog::typeLabels();
                            return $typeLabels[$models['type']];
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
     * @export 修改数据
     * @param $get
     * @return \Generator
     */
    protected function export($get)
    {

        $model = AgentRechargeLog::find();

        $model->select('agent_recharge_log.*, agent.name, agent.number, agent.mobile, user.real_name');
        $model->leftJoin('agent', 'agent.id = agent_recharge_log.agent_id');
        $model->leftJoin('user', 'agent.id = user.agent_id and user.mobile = agent.mobile');

        $model->andFilterWhere([
            'user.real_name' => $get['real_name']
        ]);

        $model->andFilterWhere([
            'agent.mobile' => $get['agent_phone']
        ]);

        $model->andFilterWhere([
            'agent.number' => $get['agent_number']
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

    /**
     * @actionRecharge 充值 预留
     * @return mixed
     */
    private function actionRecharge()
    {
		$request = Yii::$app->request;
    	if ( $request->isPost ) {

    		$data = $request->post();

            $agentModel = new Agent();
            $agent = $agentModel->findOne(['id'=>$data['id']]);

    		$rechargeTypeList = AgentRechargeLog::typeLabels();
    		$rechargeMoneyList = AgentRechargeLog::$money;
    		$rechargeMoney = isset($rechargeMoneyList[$data['money']]) ? round($data['money'], 2) : 0;

            // 生成充值记录 返回充值单号
            $AgentRechargeService = new AgentRechargeService();
            $rechargeNo = $AgentRechargeService -> addAgentRechargeLog($agent['id'], $data['type'], $rechargeMoney);

            if($rechargeNo){

                // 记录日志
                Yii::$app->services->agent->log(
                    'recharge',
                    '成功充值【' . $rechargeTypeList[$data['recharge_type']] . '】' . $rechargeMoney . '元, recharge_no:'.$rechargeNo
                );

                return $this->message("充值成功", $this->redirect(['index']));

            }

            return $this->message("充值失败,请联系管理员", $this->redirect(['index']), 'error');
    	}

		return $this->message("请求失败", $this->redirect(['index']), 'error');
    }

}