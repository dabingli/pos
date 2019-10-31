<?php
namespace common\library\api\bankCardQuery;

use Yii;
use yii\httpclient\Client;
use common\library\api\bankCardQuery\Base;

/**
 * 阿里云印刷文字识别-身份证识别
 *
 * @author Administrator
 *        
 */
class HaoService extends Base
{

    const BASE_URL = 'https://aliyuncardby4element.haoservice.com';

    public $accountNo;

    public $bankPreMobile;

    public $idCardCode;

    public $name;

    protected function getBaseUrl()
    {
        return self::BASE_URL;
    }

    public function init()
    {
        parent::init();
        $this->request->setUrl('creditop/BankCardQuery/QryBankCardBy4Element');
    }

    public function rules()
    {
        return [
            [
                [
                    'accountNo',
                    'bankPreMobile',
                    'idCardCode',
                    'name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'accountNo' => '银行卡帐号',
            'bankPreMobile' => '银行预留手机号码',
            'idCardCode' => '身份证号码',
            'name' => '持卡人姓名'
        ];
    }

    public function send()
    {
        if (! $this->validate()) {
            return false;
        }
        $data['accountNo'] = $this->accountNo;
        $data['bankPreMobile'] = $this->bankPreMobile;
        $data['idCardCode'] = $this->idCardCode;
        $data['name'] = $this->name;
        $this->request->setData($data);
        return json_decode($this->request->send()->content, true);
    }

    protected function setHeaders()
    {
        parent::setHeaders();
        $this->request->addHeaders([
            'Content-Type' => 'application/json; charset=' . static::CHARSET
        ]);
    }
}