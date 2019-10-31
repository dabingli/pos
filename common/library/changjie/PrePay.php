<?php
namespace common\library\changjie;

use Yii;
use common\library\changjie\Base;

/**
 * 同步单笔实名认证
 *
 * @author Administrator
 *        
 */
class PrePay extends Base
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
    public $TransCode = 'T10000';

    // 交易请求号
    public $OutTradeNo;

    // 企业账号
    public $CorpAcctNo;

    // BusinessType
    public $BusinessType;

    // 通用银行名称
    public $BankCommonName;

    // 银行编码
    public $BankCode;

    // 对手人账号
    public $AcctNo;

    // 对手人账户名称
    public $AcctName;

    // 交易金额
    public $TransAmt;

    // 推送地址
    public $CorpPushUrl;

    // 付费方式
    public $ChargeRole;

    // 账户类型
    public $AccountType;

    // 省
    public $Province;

    // 市
    public $City;

    // 对手行行名
    public $BranchBankName;

    // 对手行行号
    public $BranchBankCode;

    // 对手行清算行号
    public $DrctBankCode;

    // 用户在商户平台下单时候的ip地址
    public $BuyerIp;

    // 货币类型
    public $Currency;

    // 开户证件类型
    public $LiceneceType;

    // 证件号
    public $LiceneceNo;

    // 对手人手机
    public $Phone;

    // 信用卡有效期
    public $AcctExp;

    // 信用卡验证码
    public $AcctCvv2;

    // 商户级-对账分类编号
    public $CorpCheckNo;

    // 备注
    public $Summary;

    // 用途
    public $PostScript;

    public static $method = 'GET';

    public function rules()
    {
        return [
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'CorpAcctNo',
                    'BusinessType',
                    'BankCommonName',
                    'BankCode',
                    'AcctNo',
                    'AcctName',
                    'TransAmt',
                    'CorpPushUrl',
                    'ChargeRole',
                    'AccountType',
                    'Province',
                    'City',
                    'BranchBankName',
                    'BranchBankCode',
                    'DrctBankCode',
                    'BuyerIp',
                    'Currency',
                    'LiceneceType',
                    'LiceneceNo',
                    'Phone',
                    'AcctExp',
                    'AcctCvv2',
                    'CorpCheckNo',
                    'Summary',
                    'PostScript'
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
                    'TransAmt'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'CorpPushUrl',
                'url',
                'defaultScheme' => 'http|https'
            ],
            [
                [
                    'TransAmt'
                ],
                'double'
            ],
            [
                'BusinessType',
                'in',
                'range' => [
                    0,
                    1
                ]
            ],
            [
                'BusinessType',
                function ($attribute) {
                    if ($this->BusinessType == 1) {
                        if (empty($this->BankCode)) {
                            $this->addError('BankCode', '业务类型为公司时银行编码不能为空');
                            return false;
                        }
                        if (empty($this->BranchBankName)) {
                            $this->addError('BranchBankName', '业务类型为公司时对手行行名不能为空');
                            return false;
                        }
                    }
                }
            ],
            [
                'TransAmt',
                'double'
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
                'value' => 'T10000'
            ],
            [
                'ChargeRole',
                'in',
                'range' => [
                    'payer',
                    'payee'
                ]
            ],
            [
                'ChargeRole',
                'default',
                'value' => 'payer'
            ],
            [
                'AccountType',
                'in',
                'range' => [
                    '00',
                    '01'
                ]
            ],
            [
                'BuyerIp',
                'ip'
            ],
            [
                'BuyerIp',
                'default',
                'value' => Yii::$app->request->userIP
            ],
            [
                'Currency',
                'in',
                'range' => [
                    'CNY',
                    'HKD',
                    'USD'
                ]
            ],
            [
                'Currency',
                'default',
                'value' => 'CNY'
            ],
            [
                'LiceneceType',
                'in',
                'range' => [
                    '01'
                ]
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'Service' => '服务名称',
            'TransCode' => '交易码',
            'OutTradeNo' => '交易请求号',
            'CorpAcctNo' => '企业账号',
            'BusinessType' => '业务类型',
            'BankCommonName' => '通用银行名称',
            'BankCode' => '银行编码 ',
            'AcctNo' => '对手人账号',
            'AcctName' => '对手人账户名称',
            'TransAmt' => '交易金额',
            'CorpPushUrl' => '推送地址',
            'ChargeRole' => '付费方式',
            'AccountType' => '账户类型',
            'Province' => '省',
            'City' => '市',
            'BranchBankName' => '对手行行名',
            'BranchBankCode' => '对手行行号',
            'DrctBankCode' => '对手行清算行号',
            'BuyerIp' => '用户在商户平台下单时候的ip地址',
            'Currency' => '货币类型',
            'LiceneceType' => '开户证件类型',
            'LiceneceNo' => '证件号',
            'Phone' => '对手人手机号',
            'AcctExp' => '信用卡有效期',
            'AcctCvv2' => '信用卡验证码',
            'CorpCheckNo' => '商户级',
            'Summary' => '备注',
            'PostScript' => '用途'
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
        $this->AcctName = $this->rsaSignOne($this->AcctName);
        $this->AcctNo = $this->rsaSignOne($this->AcctNo);
        if (! empty($this->LiceneceNo)) {
            $this->LiceneceNo = $this->rsaSignOne($this->LiceneceNo);
        }
        if (! empty($this->AcctCvv2)) {
            $this->AcctCvv2 = $this->rsaSignOne($this->AcctCvv2);
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