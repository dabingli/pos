<?php
namespace common\services\app;

use common\components\Sms;
use common\models\user\SmsCode;
use yii;
use yii\base\Model;
use common\models\user\User;
use common\models\agent\Agent;
use common\models\CashOrder;

/**
 * 注册
 *
 * @author zhouchen
 *
 */
class UpdateMoneyForm extends Model
{

    public $type;

    public $money;

    public $fee;

    public $pay_password;

    public $activate_money;

    public $profit_money;

    protected $user;

    protected $agent;


    /**
     * @ERROR!!!
     */
    public function rules()
    {
       return [
           [
               [
                   'activate_money',
                   'profit_money',
                   'fee'
               ],
                'safe'
           ],
           [
               [
                   'money'
               ],
               'verifyMoney'
           ],

           [
               [
                   'pay_password'
               ],
               'verifyPwd'
           ],

           [
               [
                   'type',
                   'money',
                   'pay_password'
               ],
                'required',
               'message' => '{attribute}不能为空'
           ]
       ];
    }

    public function attributeLabels()
    {
        return [
            'activate_money' => '激活金额',
            'profit_money' => '分润金额',
            'type' => '提现类型',
            'money' => '提现金额',
            'pay_password' => '支付密码'

        ];
    }

    public function updateMoney(User $user)
    {
        $this->user = $user;
        if (! $this->validate()) {
            return null;
        }
//        var_dump($this->money - $this->fee + $this->agent->agent_fee);die;
        if($this->type == CashOrder::RETURN_CASH){
            $total_money = - $this->money;
            $condition=[
                'and',
                [
                    'id'=>$this->user->id
                ],
                [
                    'agent_id' => $this->user->agent_id
                ],
                ['>=', 'activate_money', -$total_money],
            ];
            $success = User::updateAllCounters(['activate_money'=> $total_money],$condition);

        }else{
            $total_money = - $this->money;
            $condition=[
                'and',
                [
                    'id'=>$this->user->id
                ],
                [
                    'agent_id' => $this->user->agent_id
                ],
                ['>=', 'profit_money', -$total_money],
            ];

            $success = User::updateAllCounters(['profit_money'=> $total_money],$condition);
        }
        $where = [
            'and',
            [
                'id' => $this->user->agent_id
            ],
            ['>=', 'balance', $this->money - $this->fee + $this->agent->agent_fee],
        ];
        $result = Agent::updateAllCounters(['balance'=> -($this->money - $this->fee + $this->agent->agent_fee)],$where);
        if(empty($result))
        {
            $this->addError('balance', '对账中，请稍后重提');
            return false;
        }
        $agent = Agent::findOne(['id'=>$this->user->agent_id]);
        $this->agent = $agent;
        if($agent->balance < $agent->warning_balance && $agent->warning_balance_times <1)
        {
            $agent->warning_balance_times = $agent->warning_balance_times + 1;
            $agent->save();
            $this->sendSms();

        }else if($agent->balance >= $agent->warning_balance && $agent->warning_balance_times > 0)
        {
            $agent->warning_balance_times = 0;
            $agent->save();
        }
        if($success && $result)
        {
            return true;
        }
        return false;
    }

    public function sendSms()
    {
        $msgContent = Yii::$app->params['sms']['sign'] . '您好，您的代付金余额不足' . $this->agent->warning_balance . '元，请及时充值。';
        $Sms = new Sms();
        $return = $Sms->send($this->agent->warning_mobile, $msgContent);
        $return = json_decode($return,true);
        if ($return['status'] == '00000')
        {
            $status = SmsCode::SUCCESS;
            $remarks = SmsCode::statusLabels()[SmsCode::SUCCESS];
            Agent::updateAllCounters(['remaining_sms_number'=>-1],['and',['id'=>$this->agent->id],['>','remaining_sms_number',0]]);
        }else{
            $status = SmsCode::FAIL;
            $remarks = SmsCode::statusLabels()[SmsCode::FAIL];
        }
        $smsCode = new SmsCode();
        $smsCode->load([
            'user_id' => $this->user->id ,
            'code' => '',
            'type' => 9,
            'return_data' => $remarks,
            'content' => $msgContent,
            'status' => $status,
            'mobile' => $this->agent->warning_mobile,
            'agent_id' => $this->user->agent_id,
        ], '');
        $smsCode->save();
    }

    public function verifyMoney($attribute){
        $agent = Agent::findOne(['id'=>$this->user->agent_id]);
        $this->agent = $agent;
        if(Agent::CASH_STATUS_CLOSE == $agent['cash_status'])
        {
            $this->agent['min_cashback'] = Yii::$app->debris->config('min_cashback');
            $this->agent['cashback_tax_point'] = Yii::$app->debris->config('cashback_tax_point');
            $this->agent['cashback_fee'] = Yii::$app->debris->config('cashback_fee');
            $this->agent['cash_fee'] = Yii::$app->debris->config('cash_fee');
            $this->agent['tax_point'] = Yii::$app->debris->config('tax_point');
            $this->agent['min_cash_amount'] = Yii::$app->debris->config('min_cash_amount');
        }
        if($this->type == CashOrder::RETURN_CASH)
        {
            if($this->money < $this->agent['min_cashback'])
            {
                $this->addError($attribute, '提现金额不能少于'.$this->agent['min_cashback'] . '元');
                return false;
            }
            if($this->money > $this->user->activate_money)
            {
                $this->addError($attribute, '余额不足');
                return false;
            }
//            if($this->money % 100 > 0){
//                $this->addError($attribute, '提现激活金额必须是100的整数倍');
//                return false;
//            }
        }else{
            if($this->money < $this->agent['min_cash_amount'])
            {
                $this->addError($attribute, '提现金额不能少于'.$agent['min_cash_amount'] . '元');
                return false;
            }
            if($this->money > $this->user->profit_money)
            {
                $this->addError($attribute, '余额不足');
                return false;
            }
//            if($this->money % 100 > 0){
//                $this->addError($attribute, '提现分润金额必须是100的整数倍');
//                return false;
//            }

        }
        return true;
    }

     public function verifyPwd($attribute)
     {
         $vaildate = Yii::$app->security->validatePassword($this->pay_password,$this->user->pay_password);
         if(!$vaildate){
             $this->addError($attribute, '支付密码不正确');
             return false;
         }
         return true;
     }

}
