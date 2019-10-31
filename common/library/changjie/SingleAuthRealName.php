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
class SingleAuthRealName extends Base
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
    public $TransCode = 'T00005';

    public $OutTradeNo;

    public $BankCode;

    public $AcctNo;

    public $AcctName;

    public $LiceneceType;

    public $LiceneceNo;

    public $BankName;

    public $BankCommonName;

    public $AccountType;

    public $AcctExp;

    public $AcctCvv2;

    public $Phone;


    public static $method = 'GET';


    public function rules()
    {
        return [
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'AcctNo',
                    'AcctName',
                    'AccountType',
                    'BankCode',
                    'Phone',
                    'AccountType',
                ],
                'safe'
            ],
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'BankCommonName',
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
                'value' => 'T10000'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'Service' => '服务名称',
            'TransCode' => '交易码',
            'OutTradeNo' => '原交易请求号',
            'AcctName' => '待查户名',
            'AcctNo' => '待查账号',
            'BankCommonName' => '通用银行名称',
            'AccountType' => '账户类型',
            'Phone' => '手机号',
            'AcctExp' => '信用卡有效期',
            'AcctCvv2' => '信用卡验证码',
            'BankName' => '开户行名称',
            'BankCode' => '开户行号',
            'LiceneceNo' => '证件号',
            'LiceneceType' => '开户证件类型'
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