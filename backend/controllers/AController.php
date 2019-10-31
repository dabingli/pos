<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
/**
 * 站点控制器
 *
 * Class SiteController
 *
 * @package backend\controllers
 */
class AController extends Controller
{

    /**
     * 默认布局文件
     *
     * @var string
     */
    public $layout = "login";

    public function actionIndex(){
        $Sms = new \common\components\Sms();
        $code = rand(100000,999999);
//        $msgContent = '验证码:'.$code .' 30分钟内有效,请不要随意告诉别人。';
//        $msgContent = '【点POS】验证码:120 30分钟内有效,请不要随意告诉别人。';

        $msgContent = '测试您在倍享流量公众号平台充值流量成功，24小时内生效即可使用1。';
        $msgContent = $msgContent.'【享惠充】';

        $Sms->send('15914358194',$msgContent);
        return $this->render('test');
    }
}
