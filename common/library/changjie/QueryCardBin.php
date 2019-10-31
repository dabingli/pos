<?php
namespace common\library\changjie;

use Yii;
use common\library\changjie\Base;

/**
 * 卡BIN信息查询
 *
 * @author Administrator
 *        
 */
class QueryCardBin extends Base
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
    public $TransCode = 'C00016';

    public $OutTradeNo;

    public $AcctNo;

    public static $method = 'GET';

    public function rules()
    {
        return [
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo',
                    'AcctNo'
                ],
                'safe'
            ],
            [
                [
                    'Service',
                    'TransCode',
                    'OutTradeNo'
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
                'value' => 'C00016'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'Service' => '服务名称',
            'TransCode' => '交易码',
            'OutTradeNo' => '交易请求号'
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
        if (! empty($this->AcctNo)) {
            $this->AcctNo = $this->rsaSignOne($this->AcctNo);
        }
        parent::afterValidate();
    }

    /**
     * 重写父类的方法，过滤掉一些属性
     * 去掉一些空值
     *
     * {@inheritdoc}
     *
     * @see \yii\base\Model::fields()
     */
    public function fields()
    {
        $data = [
            'Service',
            'TransCode',
            'OutTradeNo',
            'Version',
            'InputCharset'
        ];
        if (! empty($this->AcctNo)) {
            $data[] = 'AcctNo';
        }
        return $data;
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