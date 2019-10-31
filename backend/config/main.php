<?php
$params = array_merge(require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php');

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log'
    ],
    'defaultRoute' => 'main', // 默认控制器
    'modules' => [
        /**
         * ------ 系统模块 ------ *
         */
        'sys' => [
            'class' => 'backend\modules\sys\Module'
        ],
        /**
         * *代理商*
         */
        'agent' => [
            'class' => 'backend\modules\agent\Module'
        ],
        /**
         * *代理商*
         */
        'transaction' => [
            'class' => 'backend\modules\transaction\Module'
        ],
        /**
         * app管理
         */
        'app' => [
            'class' => 'backend\modules\app\Module'
        ],
        /**
         * 银行卡管理
         */
        'bank' => [
            'class' => 'backend\modules\bank\Module'
        ],
        /**
         * 机具类型
         */
        'product' => [
            'class' => 'backend\modules\product\Module'
        ],
        /**
         * 自动化
         */
        'automation' => [
            'class' => 'backend\modules\automation\Module'
        ]
    ],
    'components' => [
        /**
         * ------ 资源替换 ------ *
         */
        'assetManager' => [
            // 线上建议将forceCopy设置成false，如果访问量不大无所谓
            'forceCopy' => true,
            // 'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => []
                ]
            ]
        ],
        
        'request' => [
            'csrfParam' => '_csrf-backend'
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager'
        ],
        'user' => [
            'identityClass' => 'common\models\sys\Manager',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity-backend',
                'httpOnly' => true
            ],
            'loginUrl' => [
                'site/login'
            ],
            'idParam' => '__backend',
            'as afterLogin' => 'backend\behaviors\AfterLogin'
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
            'timeout' => 7200
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    // 日志存储到数据库
                    // 'class' => 'yii\log\DbTarget',
                    // 'logTable' => '{{%sys_log}}',
                    'levels' => [
                        'error',
                        'warning'
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403'
                    ],
                    'logFile' => '@runtime/logs/' . date('Y-m/d') . '.log'
                ]
            ]
        ],
        /**
         * ------ 错误定向页 ------ *
         */
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.html', // 静态
            'rules' => []
        ]
    ],
    
    'controllerMap' => [
        'provinces' => [
            'class' => 'backend\widgets\provinces\ProvincesController'
        ]
    ],
    'params' => $params
];
