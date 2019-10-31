<?php
namespace common\library\changjie;

use Yii;
use common\library\changjie\Base;

/**
 * 功能：单笔交易查询C00000
 *
 * @author Administrator
 *        
 */
class ReceiveOrder extends Base
{

    public $Service = 'cjt_dsf';

    public $TransCode = 'C00000';

    public $OutTradeNo;

    public $OriOutTradeNo;

    public static $method = 'GET';

    public function rules()
    {
        return [
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'OriOutTradeNo'
                ],
                'safe'
            ],
            [
                [
                    'OutTradeNo',
                    'OriOutTradeNo',
                    'Service',
                    'TransCode'
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
                'value' => 'C00000'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'Service' => '服务名称',
            'TransCode' => '交易码',
            'OutTradeNo' => '原交易请求号',
            'OriOutTradeNo' => '交易请求号'
        ];
    }

    static public function getUrl()
    {
        return 'mag-unify/gateway/receiveOrder.do';
    }
}