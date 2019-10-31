<?php
namespace common\services;

class Application extends Service
{

    /**
     *
     * @var array
     */
    public $childService = [
        'sys' => [
            'class' => 'common\services\sys\Sys',
            'childService' => [
                'auth' => [ // 权限
                    'class' => 'common\services\sys\Auth'
                ],
                'notify' => [ // 消息
                    'class' => 'common\services\sys\Notify'
                ]
            ]
        ],
        'agent' => [
            'class' => 'common\services\agent\Sys',
            'childService' => [
                'auth' => [ // 权限
                    'class' => 'common\services\agent\Auth'
                ],
                'notify' => [ // 消息
                    'class' => 'common\services\agent\Notify'
                ]
            ]
        ],
        'sms' => [
            'class' => 'common\services\common\Sms',
            'queueSwitch' => false // 是否丢进队列 注意如果需要请先开启执行队列
        ],
        'mailer' => [
            'class' => 'common\services\common\Mailer',
            'queueSwitch' => false // 是否丢进队列 注意如果需要请先开启执行队列
        ],
        'errorLog' => [
            'class' => 'common\services\common\ErrorLog',
            'queueSwitch' => false, // 是否丢进队列 注意如果需要请先开启执行队列
            'exceptCode' => [
                403
            ] // 除了数组内的状态码不记录，其他按照配置记录
        ],
        'provinces' => [
            'class' => 'common\services\common\Provinces'
        ],
        'push' => [
            'class' => 'common\services\common\Push'
        ]
    ];
}