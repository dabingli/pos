<?php
return [
    'version' => '2.0',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset'
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'sourceLanguage' => 'zh-cn',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'debris' => [
            'class' => 'common\components\Debris'
        ],
        /**
         * ------ 服务 ------ *
         */
        'services' => [
            'class' => 'common\services\Application'
        ]   
    ],
    'controllerMap' => [
        // 文件上传公共控制器
        'file' => [
            'class' => 'common\controllers\FileBaseController'
        ],
        'ueditor' => [
            'class' => 'common\widgets\ueditor\UeditorController'
        ]
    ]
];
