<?php
namespace common\components\queue\job;

use common\services\Withdraw;
use yii;
use yii\queue\JobInterface;
use common\library\changjie\PrePay;
use common\models\agent\Agent;
use common\models\CashOrder;
use common\models\Order;
use common\models\user\User;
use common\components\queue\job\BaseObject;
use common\library\changjie\ReceiveOrder;

class OnlineWithdraw extends BaseObject implements JobInterface
{

    public $outerTradeNo;

    protected $formData;


    // 通过formData的订单号去畅捷哪边再查询一次这个订单是否正确
    public function receiveOrder()
    {
        $model = new ReceiveOrder();
        $model->load([
            'OutTradeNo' => time(),
            'OriOutTradeNo' => $this->outerTradeNo
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        $content = json_decode($content, true);
        $this->formData = $content;
        sleep(1.5);

        if (! isset($content['AppRetcode'])) {
            return null;
        }

        if($content['AppRetcode'] == '01019999')
        {
            die;
        }

        if ($content['AppRetcode'] == '00019999') {
            return true;
        } else {
            return false;
        }
    }

    public function execute($queue)
    {
        $receiveOrder = $this->receiveOrder();
        $withdraw = new Withdraw([
            'outerTradeNo' => $this->outerTradeNo,
            'receiveOrder' => $receiveOrder
        ]);
        $withdraw->handleOrder();
    }
}