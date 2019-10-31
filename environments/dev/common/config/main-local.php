<?php
$config['components'] = [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=39.108.64.148:12306;dbname=new_test_post',
        'username' => 'dian_pos',
        'password' => 'Zy#@!qaz{2019}',
        'charset' => 'utf8mb4'
    ],
    
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true
    ]
];
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs' => [
        '*'
    ],
    'panels' => [
        'httpclient' => [
            'class' => 'yii\\httpclient\\debug\\HttpClientPanel'
        ],
        'queue' => [
            'class' => 'yii\queue\debug\Panel'
        ]
    ]
];
// 配置为本地安装的redis数据库
$config['components']['redis'] = [
    'class' => 'yii\redis\Connection',
    'hostname' => '127.0.0.1',
    'port' => 6379,
    'database' => 0
];

// 配置为本地安装的redis cache
$config['components']['cache'] = [
    'class' => 'yii\redis\Cache',
    'redis' => [
        'hostname' => '127.0.0.1',
        'port' => 6379,
        'database' => 0
    ]
];

// 配置为本地安装的redis session会话
$config['components']['session'] = [
    'class' => 'yii\redis\Session',
    'redis' => [
        'hostname' => '127.0.0.1',
        'port' => 6379,
        'database' => 0
    ]
];
// 消息队列
$config['components']['queue'] = [
    'class' => 'yii\queue\redis\Queue',
    'redis' => 'redis', // 连接组件或它的配置
    'channel' => 'queue-kdb' // Queue channel key
];
$config['components']['crontabs'] = [
    'class' => 'common\components\crontabs\CrontabsGroupComponent', // 队列使用的类
    'phpYiiCommand' => '/usr/local/php/bin/php yii' // Crontab中有可能没有php的环境
];
$config['bootstrap'] = [
    'queue' // 把这个组件注册到控制台
];
return $config;