<?php
namespace common\services\app;

use common\components\Sms;
use common\library\api\bankCardQuery\HaoService;
use common\models\agent\Agent;
use common\models\user\SmsCode;
use common\models\user\User;
use common\models\user\UserBankCard;
use Yii;
use yii\base\Model;

class AddCardForm extends Model
{

    public $cardNo;

    public $bank;

    public $identity;

    public $agent_id;

    public $user_id;

    public $code;

    public $user;

    public $pay_password;

    public $mobile;

    public function rules()
    {
        return [
            [
                [
                    'cardNo',
                    'bank',
                    'identity',
                    'code',
                    'mobile',
                    'code'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'user_id',
                    'agent_id'
                ],
                'safe'
            ],
            [
                'code',
                'app\components\captcha\code\SMSCodeValidator',
                'captchaAction' => 'v1/user/bind-code',
                'message' => '验证码错误'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'cardNo' => '银行卡号',
            'bank' => '银行卡',
            'identity' => '身份证',
            'code' => '验证码',
            'mobile' => '手机号码'
        
        ];
    }

    // 添加银行卡
    public function addCard()
    {
        if (! $this->validate()) {
            return null;
        }

        $agent = Agent::findOne(['id'=>Yii::$app->user->identity->agent_id]);

        if($agent->remaining_real_name_auth_number <= 0)
        {

            $this->addErrors([
                'error' => '认证剩余次数不足，认证失败',
            ]);
            return null;
        }

        $model = new HaoService();
        $model->load([
            'accountNo' => $this->cardNo,
            'bankPreMobile' => $this->mobile,
            'idCardCode' => $this->identity,
            'name' => Yii::$app->user->identity->real_name,
        ], '');

        if (! $model->validate()) {

            $this->addErrors([
                'error' => $model->getFirstErrors(),
            ]);
            return null;
        }

        $res = $model->send();
        if (empty($res)) {
            $this->addErrors([
                'errror' => '银行四要素认证失败',
            ]);
            return null;
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
            $user_bank_card = UserBankCard::findOne(['user_id' => $this->user_id]);

            if ($user_bank_card == null) {
                $user_bank_card = new UserBankCard();
            }

            $user_bank_card->cardNo = $this->cardNo;
            $user_bank_card->bank = $this->bank;
            $user_bank_card->identity = $this->identity;

            $user_bank_card->agent_id = $this->agent_id;
            $user_bank_card->user_id = $this->user_id;

            if ($user_bank_card->save()) {
                return true;
            } else {
                $this->addErrors($user_bank_card->getFirstErrors());
                return null;
            }
        }else{
            $this->addErrors([
                'error' => $res['result']['message'],
            ]);
            return null;
        }
    }
}
