<?php
namespace common\library\changjie;

use Yii;
use common\library\changjie\Base;

/**
 * 同步单笔待扣
 *
 * @author Administrator
 *
 */
class SinglePrePayment extends Base
{

    /**
     * 请求服务
     *
     * @var string
     */
    public $Service = 'cjt_dsf';

    /**
     * 请求编号
     *
     * @var string
     */
    public $TransCode = 'T20000';

   public $OutTradeNo;

   public $CorpAcctNo;

   public $BusinessType;

   public $BankCommonName;

   public $AcctNo;

   public $AcctName;

   public $ProtocolNo;

   public $TransAmt;

   public $CorpPushUrl;

   public $AccountType;

   public $Province;

   public $City;

   public $BranchBankName;

   public $BranchBankCode;

   public $DrctBankCode;

   public $BuyerIp;

   public $Currency;

   public $LiceneceType;

   public $LiceneceNo;

   public $Phone;

   public $AcctExp;

   public $AcctCvv2;

   public $CorpCheckNo;

   public $Summary;

   public $PostScript;

   public $Overtime;

   public $RoyaltyParameters;


    public static $method = 'GET';


    public function rules()
    {
        return [
            [
                [
                    'Service',
                    'CorpAcctNo',
                    'ProtocolNo',
                    'CorpPushUrl',
                    'AccountType',
                    'Province',
                    'City',
                    'BranchBankName',
                    'BranchBankCode',
                    'DrctBankCode',
                    'BuyerIp',
                    'Currency',
                    'Phone',
                    'AcctExp',
                    'AcctCvv2',
                    'CorpCheckNo',
                    'Summary',
                    'PostScript',
                    'Overtime',
                    'RoyaltyParameters'
                ],
                'safe'
            ],
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'BusinessType',
                    'BankCommonName',
                    'AcctNo',
                    'AcctName',
                    'TransAmt',
                    'LiceneceNo',
                    'LiceneceType'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'Service'
                ],
                'default',
                'value' => 'cjt_dsf'
            ],
            [
                [
                    'TransCode'
                ],
                'default',
                'value' => 'T20000'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'Service' => '服务名称',
            'TransCode' => '交易码',
            'OutTradeNo' => '原交易请求号',
            'CorpAcctNo' => '企业账号',
            'BusinessType' => '业务类型',
            'BankCommonName' => '通用银行名称',
            'AcctNo' => '待查账号',
            'AcctName' => '待查户名',
            'ProtocolNo' => '协议号',
            'TransAmt' => '交易金额',
            'CorpPushUrl' => '推送地址',
            'AccountType' => '账户类型',
            'Province' => '省',
            'City' => '市',
            'BranchBankName' => '对手行行名',
            'BranchBankCode' => '对手行行号',
            'DrctBankCode' => '对手行清算行号',
            'BuyerIp' => '下单ip地址',
            'Currency' => '货币类型',
            'LiceneceType' => '开户证件类型',
            'LiceneceNo' => '证件号',
            'Phone' => '手机号',
            'AcctExp' => '信用卡有效期',
            'AcctCvv2' => '信用卡验证码',
            'Summary' => '备注',
            'PostScript' => '用途',
            'Overtime' => '超时时间',
            'RoyaltyParameters' => '分账扩展字段',
        ];
    }

    /**
     * 验证之后
     *
     * {@inheritdoc} 重新父类方法，将某些参数加密
     * @see \yii\base\Model::afterValidate()
     */
    public function afterValidate()
    {
        if (! empty($this->AcctName)) {
            $this->AcctName = $this->rsaSignOne($this->AcctName);
        }
        if (! empty($this->AcctNo)) {
            $this->AcctNo = $this->rsaSignOne($this->AcctNo);
        }
        if (! empty($this->LiceneceNo)) {
            $this->LiceneceNo = $this->rsaSignOne($this->LiceneceNo);
        }
        if (! empty($this->Phone)) {
            $this->Phone = $this->rsaSignOne($this->Phone);
        }
        parent::afterValidate();
    }

    /**
     * 请求地址
     *
     * @return string
     */
    static public function getUrl()
    {
        return 'mag-unify/gateway/receiveOrder.do';
    }
}