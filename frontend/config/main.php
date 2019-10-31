<?php
$params = array_merge(require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php');

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log'
    ],
    'modules' => [
        /**
         * ------ 系统模块 ------ *
         */
        'sys' => [
            'class' => 'frontend\modules\sys\Module'
        ],
        /**
         * 机具类型
         */
        'product' => [
            'class' => 'frontend\modules\product\Module'
        ],
        /**
         * 代理商
         */
        'user' => [
            'class' => 'frontend\modules\user\Module'
        ],
        /**
         * 商户交易记录
         */
        'order' => [
            'class' => 'frontend\modules\order\Module'
        ],
        /**
         * 短信发送记录
         */
        'sms' => [
            'class' => 'frontend\modules\sms\Module'
        ],
        'statistics' => [
            'class' => 'frontend\modules\statistics\Module'
        ]
    ],
    'defaultRoute' => 'main', // 默认控制器
    'controllerNamespace' => 'frontend\controllers',
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
            'csrfParam' => '_csrf-frontend'
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\agent\AgentUser',
            'enableAutoLogin' => true,
            'authTimeout' => 3600 * 24 * 7,
            'on afterLogin' => function ($event) {
                $user = $event->identity;
                $user->login_time = time();
                $user->login_IP = \Yii::$app->request->userIp;
                return $user->save();
            },
            'on beforeLogin' => function ($event) {
                $user = $event->identity;
            },
            'identityCookie' => [
                'name' => '_identity-frontend',
                'httpOnly' => true
            ]
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403'
                    ]
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.html', // 静态
            'rules' => []
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
                    'logFile' => '@runtime/logs/' . date('Y-m/d') . '.log'
                ]
            ]
        ],
        /**
         * ------ 错误定向页 ------ *
         */
        'errorHandler' => [
            'errorAction' => 'site/error'
        ]
    ],
    'controllerMap' => [
        
        'provinces' => [
            'class' => 'backend\widgets\provinces\ProvincesController'
        ]
    ],
    'params' => $params
];
