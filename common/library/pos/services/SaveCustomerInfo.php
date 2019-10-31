<?php
namespace common\library\pos\services;

use Yii;
use common\library\pos\Base;

class SaveCustomerInfo extends Base
{

    public $customerNo;

    public $operateType;

    public $serviceNo;

    public $agentNo;

    public $shortName;

    public $phoneNo;

    public $legalPerson;

    public $identityNo;

    public $settlePeriod;

    public $mobilePosCount;

    public $areaCode;

    public $receiveAddress;

    public $mcc;

    public $creditRate;

    public $applyRate;

    public $applyUpperLimitFee;

    public $quickRate;

    public $settleAccountType;

    public $province;

    public $city;

    public $bankAccountName;

    public $bankCode;

    public $openBankName;

    public $alliedBankCode;

    public $bankAccountNo;

    public $enterprieName;

    public $businessLicenseNo;

    public $companyType;

    public $operatorName;

    public $creationDate;

    static public function getUrl()
    {
        return 'saveCustomerInfo.action';
    }

    public function rules()
    {
        return [
            [
                [
                    'customerNo',
                    'operateType',
                    'serviceNo',
                    'agentNo',
                    'shortName',
                    'phoneNo',
                    'legalPerson',
                    'identityNo',
                    'settlePeriod',
                    'mobilePosCount',
                    'areaCode',
                    'receiveAddress',
                    'mcc',
                    'creditRate',
                    'applyRate',
                    'applyUpperLimitFee',
                    'quickRate',
                    'settleAccountType',
                    'province',
                    'city',
                    'bankAccountName',
                    'bankCode',
                    'openBankName',
                    'alliedBankCode',
                    'bankAccountNo',
                    'enterprieName',
                    'businessLicenseNo',
                    'companyType',
                    'operatorName',
                    'creationDate'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                'operateType',
                'in',
                'range' => [
                    'CREATE',
                    'MODIFY'
                ]
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'agentNo' => '代理商编号',
            'serviceNo' => '服务商编号',
            'operatorType' => '操作员类型'
        ];
    }
}