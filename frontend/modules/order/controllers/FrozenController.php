<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/4
 * Time: 9:49
 */

namespace frontend\modules\order\controllers;

use Yii;
use common\models\agent\Agent;
use common\models\agent\AgentFrozenLog;
use common\models\user\User;
use moonland\phpexcel\Excel;

class FrozenController extends \frontend\controllers\MController
{
    const LIMIT = 2000;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {

        $name = $this->request->get('name');
        $number = $this->request->get('number');
        $expire_start = $this->request->get('expire_start');
        $expire_end = $this->request->get('expire_end');
        $frozen_start = $this->request->get('frozen_start');
        $frozen_end = $this->request->get('frozen_end');

        $agentModel = $this->agentModel;

        $model = AgentFrozenLog::find();
        $model->alias('a');
        $model->select('a.*');

//        $user = (new User) -> findOne(['agent_id'=>$agentModel->id]);
//        $userTable = User::tableName();
//        $model->leftJoin($userTable, "{$userTable}.id = a.user_id");

        $model->andWhere([
            'a.agent_id'=>$agentModel->id
        ]);

        $model->andFilterWhere([
            'like',
            'a.real_name',
            trim($name)
        ]);
        $model->andFilterWhere([
            'like',
            'a.user_code',
            $number
        ]);

        if (! empty($expire_start)) {
            $model->andWhere([
                '>=',
                'a.expire_at',
                strtotime($expire_start . '00:00:00')
            ]);
        }

        if (! empty($expire_end)) {
            $model->andWhere([
                '<=',
                'a.expire_at',
                strtotime($expire_end . '23:59:59')
            ]);
        }

        if (! empty($frozen_start)) {
            $model->andWhere([
                '>=',
                'a.created_at',
                strtotime($frozen_start . '00:00:00')
            ]);
        }

        if (! empty($frozen_end)) {
            $model->andWhere([
                '<=',
                'a.created_at',
                strtotime($frozen_end . '23:59:59')
            ]);
        }

        $model -> groupBy('a.id');

        $data['totalMoney'] = $model->sum('frozen_money');
        $data['total'] = $model->count();
        $model->orderBy(['created_at'=>SORT_DESC]);
        $model->limit($this->request->post('limit'));
        $model->offset($this->request->post('offset'));

        $model->asArray();

        $data['rows'] = [];
        foreach ($model->all() as $m) {
            $data['rows'][] = [
                'id' => $m['id'],
                'real_name' => $m['user_name'],
                'user_code' => $m['user_code'],
                'mobile' => $m['mobile'],
                'type_name' => $m['type_name'],
                'product_no' => $m['product_no'],
                'model' => $m['model'],
                'frozen_money' => $m['frozen_money'],
                'expire_at' => ! empty($m['expire_at']) ? date('Y-m-d H:i:s', $m['expire_at']) : '',
                'created_at' => ! empty($m['created_at']) ? date('Y-m-d H:i:s', $m['created_at']) : ''
            ];
        }
        return $this->asJson($data);

    }


    public function actionExport()
    {
        $get = $this->request->get();
        $data = $this->export($get);

        $url =  Yii::$app->request->referrer;
        ob_end_clean();//清除缓冲区,避免乱码
        $file = '代理商冻结款记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file . date('Ymd-His') . '.xls"');
        header('Cache-Control: max-age=0');

        foreach($data as $k => $rows)
        {

            Excel::export([
                'models' => $rows,
                'fileName' => '代理商冻结款记录',
                'columns' => [
                    [
                        'attribute' => 'real_name',
                        'header' => '代理商名称',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['real_name'];
                        }
                    ],
                    [
                        'attribute' => 'user_code',
                        'header' => '代理商编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['user_code'];
                        }
                    ],
                    [
                        'attribute' => 'mobile',
                        'header' => '手机号码',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['mobile'];
                        }
                    ],
                    [
                        'attribute' => 'product_no',
                        'header' => '机具编号',
                        'format' => 'text',
                        'value' => function ($models) {
                            return "\t" . $models['product_no'];
                        }
                    ],
                    [
                        'attribute' => 'type_name',
                        'header' => '机具类型',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['type_name'];
                        }
                    ],
                    [
                        'attribute' => 'frozen_money',
                        'header' => '支付金额',
                        'format' => 'text',
                        'value' => function ($models) {
                            return $models['frozen_money'];
                        }
                    ],
                    [
                        'attribute' => 'expire_at',
                        'header' => '到期时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['expire_at']);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => '冻结时间',
                        'format' => 'text',
                        'value' => function ($models) {
                            return date('Y-m-d H:i:s', $models['created_at']);
                        }
                    ],
                ]
            ]);
        }

        return yii::$app->util->alert('导出成功',$url);
        exit;
    }

    /**
     * @export 获取数据
     * @param $get
     * @return \Generator
     */
    protected function export($get)
    {
        $agentModel = $this->agentModel;

        $model = AgentFrozenLog::find();
        $model->alias('a');
        $model->select('a.*');

//        $user = (new User) -> findOne(['agent_id'=>$agentModel->id]);
//        $userTable = User::tableName();
//        $model->leftJoin($userTable, "{$userTable}.agent_id = a.agent_id");


        $model->andWhere([
            'a.agent_id'=>$agentModel->id
        ]);

        if (!empty($get['name'])) {
            $model->andWhere([
                'like',
                'a.real_name',
                $get['name']
            ]);
        }

        if (!empty($get['number'])) {
            $model->andWhere([
                'like',
                'a.user_code',
                $get['number']
            ]);
        }

        if (!empty($get['expire_start'])) {
            $model->andWhere([
                '>=',
                'a.expire_at',
                strtotime($get['expire_start'] . '00:00:00')
            ]);
        }

        if (!empty($get['expire_end'])) {
            $model->andWhere([
                '<=',
                'a.expire_at',
                strtotime($get['expire_end'] . '23:59:59')
            ]);
        }

        if (!empty($get['frozen_start'])) {
            $model->andWhere([
                '>=',
                'a.created_at',
                strtotime($get['frozen_start'] . '00:00:00')
            ]);
        }

        if (!empty($get['frozen_end'])) {
            $model->andWhere([
                '<=',
                'a.created_at',
                strtotime($get['frozen_end'] . '23:59:59')
            ]);
        }

        $model -> groupBy('a.id');

        $count = $model->count();

        $limit=self::LIMIT;

        $model->limit($limit);
        $model->asArray();
        for($i=0;$i<=$count;){
            $model->limit($limit)->offset($i);
            $i=$i+$limit;
            yield $model->all();
        }

    }




}
