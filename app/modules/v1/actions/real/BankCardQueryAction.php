<?php
namespace app\modules\v1\actions\real;

use common\components\Sms;
use common\models\agent\Agent;
use common\models\user\SmsCode;
use common\models\user\User;
use common\models\user\UserBankCard;
use common\models\user\UserIdentityAudit;
use common\models\user\UserIdentityAuditLog;
use yii;
use yii\base\Action;
use yii\httpclient\Client;
use common\library\api\bankCardQuery\HaoService;


class BankCardQueryAction extends Action
{

    public $accountNo;

    public $bankPreMobile;

    public $idCardCode;

    public $name;

    public $birthday;

    public $identity_front_images;

    public $identity_back_images;

    public $identity_personal_images;

    public $hold_identity_images;

    public $code;

    public $bank;

    public $mobile;

    public function beforeRun()
    {
        $this->bankPreMobile = $this->mobile;
        return true;
    }

    public function run()
    {
        if (User::AUTH_NOT != Yii::$app->user->identity->is_authentication) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '该用户实名验证状态不是未实名状态'
                ],
                'data' => []
            ];
        }
        
        $userIdentityAuditLogModel = new UserIdentityAuditLog();
        $userIdentityAuditLogModel->user_id = Yii::$app->user->id;
        $userIdentityAuditLogModel->agent_id = Yii::$app->user->identity->agent_id;
        $userIdentityAuditLogModel->type = UserIdentityAuditLog::IDENTITY;
        $agent = Agent::findOne(['id'=>Yii::$app->user->identity->agent_id]);
        if ($agent->remaining_real_name_auth_number <= 0) {
            $userIdentityAuditLogModel->status = UserIdentityAuditLog::FAIL;
            $userIdentityAuditLogModel->description = '认证剩余次数不足，认证失败';
            $userIdentityAuditLogModel->save();
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '实名认证充值中，请稍后重试！'
                ],
                'data' => []
            ];
        }
        
        $model = new HaoService();
        $model->load([
            'accountNo' => $this->accountNo,
            'bankPreMobile' => $this->bankPreMobile,
            'idCardCode' => $this->idCardCode,
            'name' => $this->name
        ], '');
        
        if (! $model->validate()) {
            
            return [
                'status' => 0,
                'code' => 0,
                'message' => $model->getFirstErrors(),
                'data' => []
            ];
        }
        $res = $model->send();
        if (empty($res)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '银行四要素认证失败'
                ],
                'data' => []
            ];
        }
        if ($res['error_code'] == 0) {
            if($agent->remaining_real_name_auth_number == $agent->warning_real_name_auth_number)
            {
                $msgContent = Yii::$app->params['sms']['sign'] . '您好，您的实名认证次数不足' . $agent->remaining_real_name_auth_number . '次，请及时充值。';
                $Sms = new Sms();
                $response = $Sms->send($agent->warning_mobile, $msgContent);

                $smsCode = new SmsCode();
                $smsCode->load([
                    'user_id' => Yii::$app->authenticator->getUser() ? Yii::$app->authenticator->getUser() : 0,
                    'code' => '',
                    'type' => 8,
                    'return_data' => '成功',
                    'content' => $msgContent,
                    'status' => 1,
                    'mobile' => $agent->warning_mobile,
                    'agent_id' => !empty(Yii::$app->user->identity->agent_id) ? Yii::$app->user->identity->agent_id : 0,
                ], '');
                $smsCode->save();

                Agent::updateAllCounters(['remaining_sms_number'=>-1],['and',['id'=>Yii::$app->user->identity->agent_id],['>','remaining_sms_number',0]]);
            }
            $agent->remaining_real_name_auth_number = $agent->remaining_real_name_auth_number - 1;
            $agent->save();
        }
        if ($res['result']['result'] == 'T') {
            $userIdentityAuditLogModel->status = UserIdentityAuditLog::SUCCESS;
            $userIdentityAudit = new UserIdentityAudit();
            $image = json_encode([
                'identity_front_images' => $this->identity_front_images,
                'identity_back_images' => $this->identity_back_images,
                'identity_personal_images' => $this->identity_personal_images,
                'hold_identity_images' => $this->hold_identity_images,
            ]);
            $userIdentityAudit->load([
                'agent_id' => Yii::$app->user->identity->agent_id,
                'user_id' => Yii::$app->user->id,
                'real_name' => $this->name,
                'identity_card' => $this->idCardCode,
                'cardNo' => $this->accountNo,
                'code' => $this->code,
                'type' => 1,
                'image' => $image
            ], '');
            $transaction = Yii::$app->db->beginTransaction();
            if (! $userIdentityAudit->save()) {
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'code' => 0,
                    'message' => $userIdentityAudit->getFirstErrors(),
                    'data' => []
                ];
            }
            Yii::$app->user->identity->is_authentication = User::SUBMISSION;
            if (! Yii::$app->user->identity->save()) {
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'code' => 0,
                    'message' => Yii::$app->user->identity->getFirstErrors(),
                    'data' => []
                ];
            }
            $userBankCard = new UserBankCard();
            $userBankCard-> agent_id = $agent['id'];
            $userBankCard->user_id = Yii::$app->user->id;
            $userBankCard->cardNo = $this->accountNo;
            $userBankCard->bank = $this->bank;
            if(!$userBankCard->save()){
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'code' => 0,
                    'message' => $userBankCard->getFirstErrors(),
                    'data' => []
                ];
            }
            $transaction->commit();
        } else {
            $userIdentityAuditLogModel->status = UserIdentityAuditLog::FAIL;
        }
        $userIdentityAuditLogModel->description = $res['result']['message'];
        $userIdentityAuditLogModel->cardNo = $res['result']['accountNo'];
        $userIdentityAuditLogModel->identity_card = $res['result']['idCardCore'];
        $userIdentityAuditLogModel->mobile = $res['result']['bankPreMobile'];
        $userIdentityAuditLogModel->real_name = $res['result']['name'];
        $userIdentityAuditLogModel->type = 1;
        $userIdentityAuditLogModel->save();

        if($res['result']['result'] == 'T')
        {
            return [
                'status' => 0,
                'code' => 200,
                'message' => '提交成功,等待审核',
                'data' => ''
            ];
        }
        return [
            'status' => 0,
            'code' => 0,
            'message' => $res['result']['message'],
            'data' => $userIdentityAuditLogModel
        ];
    }
}