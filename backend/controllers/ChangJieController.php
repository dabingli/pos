<?php
namespace backend\controllers;

use common\library\changjie\PrePay;
use common\library\changjie\SingleAuthRealName;
use common\library\changjie\SinglePrePayment;
use Yii;
use yii\web\Controller;

/**
 * 畅捷调试
 *
 * @author Administrator
 *        
 */
class ChangJieController extends Controller
{

    /**
     * 默认布局文件
     *
     * @var string
     */
    public $layout = "login";

    public function actionReceiveOrder()
    {
        $model = new \common\library\changjie\ReceiveOrder();
        $model->load([
            'OutTradeNo' => time(),
            'OriOutTradeNo' => '20170801194003855355401'
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionQueryBalance()
    {
        $model = new \common\library\changjie\QueryBalance();
        $model->load([
            // 'AcctNo' => 1,
            'AcctName' => '2',
            'OutTradeNo' => time()
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionPrePay()
    {
        $model = new PrePay();
        $model->load([
            'AcctNo' => '1',
            'BusinessType' => 0,
            'BankCode' => 'ICBC',
            'TransAmt' => 0.01,
            'AcctName' => '1',
            'OutTradeNo' => time(),
            'BankCommonName' => '1',
            'BranchBankName' => '中国建设银行广州东山广场分理处',
            'CorpPushUrl' => 'http://172.20.11.16',
            'AccountType' => '00'
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionQuerySingleAuthRealName()
    {
        $model = new \common\library\changjie\QuerySingleAuthRealName();
        $model->load([
            'OriOutTradeNo' => '20170802091917762601734',
            'OutTradeNo' => time()
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionQueryCardBin()
    {
        $model = new \common\library\changjie\QueryCardBin();
        $model->load([
            'AcctNo' => '6228270087545893977',
            'OutTradeNo' => time()
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionSingleAuthRealName()
    {
        $model = new SingleAuthRealName();
        $model->load([
            'BankCommonName' => '中国工商银行',
            'OutTradeNo' => time(),
            'AcctNo' => 1,
            'AcctName' => '2',
            'LiceneceType' => '01',
            'LiceneceNo' => '111',
            'AccountType' => '01'
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }

    public function actionSinglePrePayment()
    {
        $model = new SinglePrePayment();
        $model->load([
            'BankCommonName' => '中国工商银行',
            'OutTradeNo' => time(),
            'AcctNo' => 1,
            'AcctName' => '2',
            'LiceneceType' => '01',
            'LiceneceNo' => '111',
            'AccountType' => '01',
            'BusinessType' => 0,
            'TransAmt' => 0.01,
            'Phone' => '1212'
        ], '');
        $http = $model->http();
        $content = $http->send()->content;
        print_r($content);
        return $this->render('/test/test');
    }
}
