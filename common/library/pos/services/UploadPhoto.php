<?php
namespace common\library\pos\services;

use Yii;
use common\library\pos\Base;

class UploadPhoto extends Base
{

    public $agentNo;

    public $serviceNo;

    public $operatorType;

    static public function getUrl()
    {
        return 'uploadPhoto.action';
    }

    public function rules()
    {
        return [
            [
                [
                    'customerNo',
                    'agentNo',
                    'serviceNo',
                    'file',
                    'type'
                
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