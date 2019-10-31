<?php
namespace app\modules\v1\actions\real;

use yii;
use yii\base\Action;
use yii\httpclient\Client;
use common\library\api\idCard\YinShuaWenZi;
use common\models\entities\real\UserRealExamineLog;
use app\models\entities\User;

class IdcardImageAction extends Action
{

    public $image;

    public function run()
    {
        if (User::AUTH_NOT != Yii::$app->user->identity->is_authentication) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '该用户实名验证状态不是未实名'
                ],
                'data' => []
            ];
        }
        $userRealExamineLogModel = new UserRealExamineLog();
        $userRealExamineLogModel->user_id = Yii::$app->user->id;
        $userRealExamineLogModel->agent_id = Yii::$app->params['agentModel']->id;
        $userRealExamineLogModel->type = UserRealExamineLog::IDENTITY;
        if (Yii::$app->params['agentModel']->certified_wallet <= 0) {
            $userRealExamineLogModel->status = UserRealExamineLog::FAIL;
            $userRealExamineLogModel->examine_explain = '认证资金帐户余额不足，认证失败';
            $userRealExamineLogModel->save();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '身份证认证失败'
                ],
                'data' => []
            ];
        }
        // 先看本地有没有该图片
        $rootPath = Yii::getAlias('@public');
        $path = $rootPath . '/' . $this->image;
        $pathinfo = pathinfo($path);
        if (! is_file($path)) {
            $client = new Client();
            $request = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(Yii::$app->params['oss']['localUrl'] . '/' . $this->image);
            $response = $request->send();
            if ($response->isOk) {
                if (! is_dir($pathinfo['dirname'])) {
                    mkdir($pathinfo['dirname'], 0777, true);
                }
                file_put_contents($path, $response->content);
            }
        }
        $model = new YinShuaWenZi();
        $model->load([
            'image' => $path,
            'side' => YinShuaWenZi::FACE
        ], '');
        $res = $model->send();
        if (! empty($res) && $res['success']) {
            $userRealExamineLogModel->status = UserRealExamineLog::SUCCESS;
            $userRealExamineLogModel->examine_explain = '成功';
            $userRealExamineLogModel->real_name = $res['name'];
            $userRealExamineLogModel->realcertno = $res['num'];
            $userRealExamineLogModel->sex = $res['sex'] == '男' ? 1 : $res['sex'] == '女' ? 2 : 3;
            $userRealExamineLogModel->nationality = $res['nationality'];
            Yii::$app->params['agentModel']->certified_wallet = Yii::$app->params['agentModel']->certified_wallet - Yii::$app->params['ocridcard']['money'];
            Yii::$app->params['agentModel']->save();
        } else {
            $userRealExamineLogModel->status = UserRealExamineLog::FAIL;
            $userRealExamineLogModel->examine_explain = '认证失败';
        }
        $userRealExamineLogModel->save();
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $userRealExamineLogModel
        ];
    }
}