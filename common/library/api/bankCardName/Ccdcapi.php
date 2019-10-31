<?php
namespace common\library\api\bankCardName;

use Yii;
use yii\httpclient\Client;
use common\library\api\bankCardName\Base;

/**
 * 阿里提供的免费查询银行编码
 *
 * @author Administrator
 *        
 */
class Ccdcapi extends Base
{

    const BASE_URL = 'https://ccdcapi.alipay.com';

    public $cardNo;

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
        $this->request->setUrl('validateAndCacheCardInfo.json');
    }

    public function rules()
    {
        return [
            ['cardNo','required','message' => '银行卡号不能为空'],
            ['cardNo','number','message'=>'银行卡号必须是数字'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cardNo' => '银行卡帐号',
        ];
    }

    public function send()
    {
        if (! $this->validate()) {
            return false;
        }
        $data['cardNo'] = $this->cardNo;
        $data['cardBinCheck'] = 'true';
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