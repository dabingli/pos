<?php
namespace app\components\captcha\code;

use common\components\Sms;
use common\models\agent\Agent;
use common\models\user\User;
use Yii;
use yii\validators\Validator;
use yii\web\Response;
use common\models\user\SmsCode;

abstract class BaseAction extends \yii\base\Action
{

    public $authTimeout = 180;

    public $codeId;

    public $fixedVerifyCode;

    public $length = 6;

    public $interval = 60;

    protected $verifyCode;

    public $content;

    public $mobile;

    public $parent_mobile;

    public function getVerifyCode()
    {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }
        $pattern = '1234567890';
        $verifyCode = '';
        for ($i = 0; $i < $this->length; $i ++) {
            $verifyCode .= $pattern{rand(0, 9)};
        }
        return $verifyCode;
    }

    public function getAuthenName()
    {
        $mobile = $this->mobile ?: $this->userMobile;
        return md5(static::class . $mobile);
    }

    public function validate($input, $caseSensitive)
    {
        $authenName = $this->getAuthenName();
        $code = Yii::$app->redis->get($authenName);
        if (empty($code)) {
            return null;
        }
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        if($valid == true){
            Yii::$app->redis->del($this->authenName);
        }

        return $valid;
    }
    public function getUserMobile(){
        return Yii::$app->user->identity->mobile;
    }

    public function run()
    {
        if(empty($this->mobile))
        {
            $this->mobile = $this->userMobile;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($this->mobile)) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '手机号码不能为空'
                ],
                'data' => []
            ];
        }
        $mobile = $this->mobile;
        $authenName = $this->getAuthenName();
        $time = Yii::$app->redis->get($mobile);

        if (time() - $this->interval < $time) {
            return [
                'status' => 0,
                'code' => 0,
                'message' => [
                    '验证码需要隔' . $this->interval . '秒才能重新获取'
                ],
                'data' => []
            ];
        }

        $this->verifyCode = $this->getVerifyCode();
        $msgContent = $this->content . $this->verifyCode . '（切勿告知他人），请在' . $this->authTimeout/60 . '分钟内完成验证。';

        $user = User::findOne(['mobile'=>$this->mobile]);

        if(empty($user) && !empty($this->parent_mobile))
        {
            $user = User::findOne(['mobile'=>$this->parent_mobile]);
        }

        if(!empty($user))
        {
            $agent = Agent::findOne(['id'=>$user->agent_id]);
            if($agent->remaining_sms_number <= 0)
            {
                return [
                  'status' => 0,
                  'code' => 0,
                  'message' => '短信充值中，请稍后重试！',
                  'data' =>''
                ];
            }
        }
//        发送短信
        $Sms = new Sms();
        $response = $Sms->send($mobile, $msgContent);
        $response = json_decode($response,true);
        if ($response['status'] == '00000') {
            $status = SmsCode::SUCCESS;
            $remarks = SmsCode::statusLabels()[SmsCode::SUCCESS];
            if(!empty($user)){
               $this->warningSms($user);
            }
        } else {
            $status = SmsCode::FAIL;
            $remarks = SmsCode::statusLabels()[SmsCode::FAIL];
        }

        $smsCode = new SmsCode();
        $smsCode->load([
            'user_id' => !empty($user) ? $user->id : 0,
            'code' => $this->verifyCode,
            'type' => $this->codeId,
            'return_data' => $remarks,
            'content' => $msgContent,
            'status' => $status,
            'mobile' => $mobile,
            'agent_id' => !empty($user) ? $user->agent_id : 0,
        ], '');
        $smsCode->save();

        Yii::$app->redis->set($authenName, $this->verifyCode);
        Yii::$app->redis->expire($authenName, $this->authTimeout);
        Yii::$app->redis->set($mobile,time());
        Yii::$app->redis->expire($mobile, $this->interval);
        
        return [
            'status' => 0,
            'code' => 200,
            'message' => [
                '验证码获取成功'
            ],
            'data' => []
        ];
    }

//    短信不足提醒
    public function warningSms($user)
    {
        Agent::updateAllCounters(['remaining_sms_number'=>-1],['and',['id'=>$user->agent_id],['>','remaining_sms_number',0]]);
        $agent = Agent::findOne(['id' => $user->agent_id]);
        if($agent->remaining_sms_number == $agent->warning_sms_number) {
            $msgContent = Yii::$app->params['sms']['sign'] . '您好，您的短信剩余条数不足' . $agent->remaining_sms_number . '条，请及时充值。';
            $Sms = new Sms();
            $return = $Sms->send($agent->warning_mobile, $msgContent);
            $return = json_decode($return,true);
            if ($return['status'] == '00000') {
                $status = SmsCode::SUCCESS;
                $remarks = SmsCode::statusLabels()[SmsCode::SUCCESS];
                Agent::updateAllCounters(['remaining_sms_number' => -1], ['and', ['id' => $user->agent_id], ['>', 'remaining_sms_number', 0]]);
            } else {
                $status = SmsCode::FAIL;
                $remarks = SmsCode::statusLabels()[SmsCode::FAIL];
            }
            $smsCode = new SmsCode();
            $smsCode->load([
                'user_id' => $user->id,
                'code' => '',
                'type' => 7,
                'return_data' => $remarks,
                'content' => $msgContent,
                'status' => $status,
                'mobile' => $agent->warning_mobile,
                'agent_id' => $user->agent_id,
            ], '');
            $smsCode->save();
        }
    }
}