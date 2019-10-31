<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\CashOrder;
use common\components\queue\job\OnlineWithdraw;

class ChangJieAsyncController extends Controller
{

    // 异步回调
    /**
     * 异步回调地址需要保证该用户提现数据正确率
     * 第一步，畅捷请求过来之后，先进行处理，先要将该订单去畅捷查询一次，是否成功
     * 第二步，消息列表在24小时之后再将，如果该订单还是会处理，需要将该订单去畅查询一次是否成功再处理
     * 第三步，定时任务，48小时之后还未处理的订单，需要将该订单去畅捷查询一次，是否成功，再处理
     * 以上三步都是为了保存用户的提现金额准确性，如果服务器发起提现操作，但畅捷服务器并没有发送异步请求到服务器上来，第二步可以保存在24小
     * 时之后用消息队列去查询该订单是否在畅捷交易成功，如果成功修改用户提现记录为成功，
     * 如果自己服务器重启了redis或者redis数据丢失了，定时任务会在48小时之后去处理该订单，将48小时之后按消息队列的方式去处理
     * 以上三步才能准确保证用户的提现金额是不会有问题的
     *
     * @return string
     */
    public function actionOnlineWithdraw()
    {
        $model = CashOrder::find();
        $model->andWhere([
            'status' => CashOrder::HANDLE,
            'handle' => CashOrder::AUDIT_SUCCESS
        ]);
        $model->andWhere([
            '<=',
            'created_at',
            time() - 3600 * 48
        ]);
        foreach ($model->all() as $m) {
            try {
                $onlineWithdraw = new OnlineWithdraw([
                    'outerTradeNo' => $m->order
                ]);
                $onlineWithdraw->execute(Yii::$app->queue);
            } catch (\Exception $e) {}
        }
    }
}