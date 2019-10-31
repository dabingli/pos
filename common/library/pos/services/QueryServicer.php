<?php
namespace common\library\pos\services;

use Yii;
use common\library\pos\Base;

class QueryServicer extends Base
{

    public $agentNo;

    public $serviceNo;

    public $operatorType;

    static public function getUrl()
    {
        return 'queryServicer.action';
    }

    public function rules()
    {
        return [
            [
                [
                    'agentNo',
                    'serviceNo',
                    'operatorType'
                
                ],
                'required',
                'message' => '{attribute}不能为空'
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