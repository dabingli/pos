<?php
namespace common\library\changjie;

use Yii;
use common\library\changjie\Base;

/**
 * 单笔实名认证查询
 *
 * @author Administrator
 *        
 */
class QuerySingleAuthRealName extends Base
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
    public $TransCode = 'C00013';

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
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'OriOutTradeNo'
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
                'value' => 'C00013'
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