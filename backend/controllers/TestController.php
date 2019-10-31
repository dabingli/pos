<?php
namespace backend\controllers;

use common\models\entities\app\AppAdvertise;
use common\models\user\User;
use Yii;
use yii\web\Controller;

class TestController extends Controller
{

    /**
     * 默认布局文件
     *
     * @var string
     */
    public $layout = "login";

    public function actionTest()
    {
        //Yii::$app->session->set('t', 'tt');
        echo Yii::$app->session->get('t');
        echo 1;
    }

    public function actionQueryServicer()
    {
        $model = new \common\library\pos\services\QueryServicer();
        $model->load([
            'agentNo' => 111,
            'serviceNo' => '1111',
            'operatorType' => ''
        ], '');
        $model->validate();
        $res = $model->http()->send()->content;
        print_r($res);
        return $this->render('test');
    }

    public function actionSaveCustomerInfo()
    {
        $total_money = - 7;
        $condition = [
            [
                'id' => 44
            ],
            [
                '>=',
                'profit_money',
                7
            ]
        ];
        $condition = [
            'and',
            [
                'id' => 44
            ],
            [
                '>=',
                'profit_money',
                7
            ]
        ];
        // $condition=" id = 44 and profit_money>=7";
        $user = User::updateAllCounters([
            'profit_money' => $total_money
        ], $condition);
        // var_dump($user);die;
        // $model = new \common\library\pos\services\SaveCustomerInfo();
        // $model->load([
        // 'customerNo' => 'DBQ2870951',
        // 'operateType' => 'CREATE',
        // 'serviceNo' => '132456',
        // 'agentNo' => 'agentNo',
        // 'shortName' => 'shortName',
        // 'phoneNo' => 'phoneNo',
        // 'legalPerson' => 'legalPerson',
        // 'identityNo' => 'identityNo',
        // 'settlePeriod' => 'settlePeriod',
        // 'mobilePosCount' => 'mobilePosCount',
        // 'areaCode' => 'areaCode',
        // 'receiveAddress' => 'receiveAddress',
        // 'mcc' => 'mcc',
        // 'creditRate' => 'creditRate',
        // 'applyRate' => 'applyRate',
        // 'applyUpperLimitFee' => 'applyUpperLimitFee',
        // 'quickRate' => 'quickRate',
        // 'settleAccountType' => 'settleAccountType',
        // 'province' => 'province',
        // 'city' => 'city',
        // 'bankAccountName' => 'bankAccountName',
        // 'bankCode' => 'bankCode',
        // 'openBankName' => 'openBankName',
        // 'alliedBankCode' => 'alliedBankCode',
        // 'bankAccountNo' => 'bankAccountNo',
        // 'enterprieName' => 'enterprieName',
        // 'businessLicenseNo' => 'businessLicenseNo',
        // 'companyType' => 'companyType',
        // 'operatorName' => 'operatorName',
        // 'creationDate' => 'creationDate'
        // ], '');
        // $model->validate();
        // $res = $model->http()->send()->content;
        // print_r($res);
        return $this->render('test');
    }
}
