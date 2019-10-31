<?php
namespace backend\controllers;

use common\library\uni_push\SingleNotification;
use Yii;
use yii\web\Controller;
use common\library\uni_push\Notification;
use common\services\common\AppMessage;
use common\components\queue\job\AppMessage as AppMessageQueue;
use common\models\product\Product;
use common\models\product\ProductLog;
use common\models\Transaction;

/**
 * 畅捷调试
 *
 * @author Administrator
 *        
 */
class UniPushController extends Controller
{

    /**
     * @actionNotification 群发通知
     * @return string
     */
    public function actionNotification()
    {
        $model = new Notification();
        $model->load([
            'title' => '测试标题',
            'content' => '20170801194003855355401',
            'payload' => ['id'=>20],
        ], '');
        $content = $model->send();
        print_r($content);
        return $this->render('/test/test');
    }


    /**
     * @actionSingleNotification 单个用户通知
     * @return string
     */
    public function actionSingleNotification()
    {
        $model = new SingleNotification();
        $model->load([
            'clientId' => 'e093bb6c77ed9d91cb1d2886b43f47ef',
            'title' => '测试标题',
            'content' => '20170801194003855355401',
            'payload' => ['id'=>87],
        ], '');
        $content = $model->send();
        print_r($content);
        return $this->render('/test/test');
    }

    /**
     * @actionTest 通知
     * @return string
     */
    public function actionTest()
    {
        var_dump((new AppMessage(2260))->send());
    }

    /**
     * @actionTest 通知
     * @return string
     */
    public function actionSingle()
    {
        var_dump((new AppMessage(2169))->sendSingleByClientId('fa67610ef407a86f0d3ac7790bc2913f'));

    }

    public function actionSingleOld(){
        var_dump((new AppMessage(2169))->sendSingleByClientId('101f0445a8e944c1d6d3c6334f43baec'));
    }
    /**
     * 恢复机具数据
     */
    public function actionRecover(){
        $product = Product::find();
        $product->andWhere(['like','product_no','026%',false]);
        $products = $product->asArray()->all();

        $log = ProductLog::find();
        $log->with('new');
        $log->orderBy('id desc');
//        $log->andWhere(['like','product_no','%026',false]);
        $logs = $log->asArray()->all();
        $dataLogs = $oldLogs = [];
        foreach ($logs as $log){
            if(!isset($dataLogs[$log['product_no']])){
                $dataLogs[$log['product_no']] = $log;
            }else{
                $oldLogs[$log['product_no']] = $log;
            }
        }
        $new = [];
        $db = Yii::$app->db->beginTransaction();
        try{
            foreach ($products as $product){
                if($dataLogs[$product['product_no']]['user_code'] != $product['user_code']){
                    $pro = Product::findOne([
                        'id' => $product['id']
                    ]);
                    $pro->user_code = $dataLogs[$product['product_no']]['user_code'];
                    $pro->user_name = $dataLogs[$product['product_no']]['user_name'];
                    $pro->status = $dataLogs[$product['product_no']]['status'];
                    $pro->send_time = $dataLogs[$product['product_no']]['send_time'];
                    $pro->user_id = $dataLogs[$product['product_no']]['new']['id'];
                    $pro->save();
                }

            }
            $db->commit();
        }catch (\Exception $e){
            $db->rollback();
        }
        var_dump($dataLogs);die;
    }

    public function actionProduct(){
        $sql = "SELECT
                u.phone AS mobile,
                u.serialNo AS product_no,
                u.agent_id,
                u.user_id,
                p.model,
                p.type_id AS agent_product_type_id,
                s.real_name AS user_name,
                u.bindingTime,
                s.user_code
            FROM
                merchant_user AS u
            LEFT JOIN product AS p ON u.serialNo = p.product_no
            LEFT JOIN `user` AS s ON s.id = u.user_id
            WHERE
                u.user_id != p.user_id";
        $connection  = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $res = $command->queryAll();
        foreach($res as &$v){
            $v['name'] = '广东迅付企业管理有限公司';
            $v['status'] = '2';
            $v['serial'] = 'XF'.date('Ymd',$v['bindingTime']).'3215';
            $v['send_time'] = $v['bindingTime'] - (8*3600);
            $v['activate_status'] = '2';
            unset($v['bindingTime']);
        }

        $success = Yii::$app->db->createCommand()
            ->batchInsert(ProductLog::tableName(), [
                'mobile',
                'product_no',
                'agent_id',
                'user_id',
                'model',
                'agent_product_type_id',
                'user_name',
                'user_code',
                'name',
                'status',
                'serial',
                'send_time',
                'activate_status'
            ], $res)
            ->execute();
        var_dump($success);die;
    }
    //贷记卡税率金额错误
    public function actionPos(){
        set_time_limit(0);
        $model = Transaction::find();
        $model->where([
            'cardType'=>['CREDIT_CARD', 'SEMI_CREDIT_CARD']
        ]);

        foreach ($model->all() as $v){
            $v->fee = $v->txAmt * $v->rate;
            $v->amountArrives = $v->txAmt - $v->fee;
            $v->save();
        }
    }
}
