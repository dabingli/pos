<?php
$params = array_merge(require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php');

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'bootstrap' => [
        'log'
    ],
    'controllerNamespace' => 'app\controllers',
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module'
        ]
    ],
    // '*'
    
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
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
                        'yii\web\HttpException:401',
                        'yii\web\HttpException:403'
                    ],
                    'logFile' => '@runtime/logs/' . date('Y-m/d') . '.log'
                ]
            ]
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\user\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
            'authTimeout' => 3600,
            'identityCookie' => [
                'name' => '_identity-app',
                'httpOnly' => true
            ],
            'on afterLogin' => function ($event) {
                $user = $event->identity;
                $user->login_time = time();
                $user->login_IP = Yii::$app->request->userIp;
                Yii::$app->authenticator->setUser($user);
                return $user->save();
            },
            'on afterLogout' => function ($event) {
                $user = $event->identity;
                $user->generateAccessToken();
                Yii::$app->authenticator->clearUser();
                return $user->save();
            }
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend'
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'GET,POST,PUT pos/index' => 'pos/index',
                'GET,POST,PUT pos/order' => 'pos/order',
                'GET,POST,PUT site/index' => 'site/index',
                'POST v1/site/get-access-token' => 'v1/site/get-access-token',
                'POST site/system' => 'site/system',
                'GET,POST,PUT v1/site/captcha' => 'v1/site/captcha',
                'POST,PUT file/images' => 'file/images',
                'POST,PUT file/files' => 'file/files',
                'POST,PUT file/videos' => 'file/videos',
                'POST,PUT file/base64' => 'file/base64',
                'GET site/register' => 'site/register',
                'POST site/register-code' => 'site/register-code',
                'POST site/register-do' => 'site/register-do',
                'GET,POST,PUT chang-jie-async/online-withdraw' => 'chang-jie-async/online-withdraw',
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/user',
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'POST signup-code' => 'signup-code',
                        'POST set-up-password-code' => 'set-up-password-code',
                        'POST logout' => 'logout',
                        'POST signup' => 'signup',
                        'POST info' => 'info',
                        'POST set-up-password' => 'set-up-password',
                        'POST set-up-pay-password' => 'set-up-pay-password',
                        'POST set-up-pay-password-code' => 'set-up-pay-password-code',
                        'POST set-profile' => 'set-profile',
                        'POST authentication' => 'authentication',
                        'POST edit-info' => 'edit-info',
                        'POST feed-back' => 'feed-back',
                        'POST bank-card' => 'bank-card',
                        'POST bank-card-name' => 'bank-card-name',
                        'POST bind-code' => 'bind-code',
                        'POST add-card' => 'add-card',
                        'POST forget-pwd' => 'forget-pwd',
                        'POST set-up-forget_pwd-code' => 'set-up-forget_pwd-code',
                        'POST not-register-detail' => 'not-register-detail',
                        'POST register' => 'register',
                        'POST registered-list' => 'registered-list',
                        'POST registered-detail' => 'registered-detail',
                        'POST register-num' => 'register-num',
                        'POST not-register-num' => 'not-register-num',
                        'POST not-registered-list' => 'not-registered-list',
                        'POST frozen-profit' => 'frozen-profit',
                        'POST user-bank-card' => 'user-bank-card',
                        'POST profit' => 'profit',
                        'POST save-client-id' => 'save-client-id'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/index',
                    'extraPatterns' => [
                        'POST images' => 'images',
                        'POST system' => 'system',
                        'POST system-business' => 'system-business',
                        'POST region-son' => 'region-son',
                        'POST region-parent' => 'region-parent',
                        'POST advertise' => 'advertise',
                        'POST featured' => 'featured',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/rank',
                    'extraPatterns' => [
                        'POST activate-list' => 'activate-list',
                        'POST total-amount-list' => 'total-amount-list'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/product',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST detail' => 'detail',
                        'POST select-product-send' => 'select-product-send',
                        'POST select-product-back' => 'select-product-back',
                        'POST send-log' => 'send-log',
                        'POST back-log' => 'back-log',
                        'POST send-update' => 'send-update',
                        'POST back-update' => 'back-update',
                        'POST send' => 'send',
                        'POST back' => 'back',
                        'POST batch-send' => 'batch-send',
                        'POST batch-back' => 'batch-back'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/merchant',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST detail' => 'detail',
                        'POST statistics' => 'statistics',
                        'POST statistics-list' => 'statistics-list'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/share',
                    'extraPatterns' => [
                        'POST image-list' => 'image-list'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/order',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST statistics' => 'statistics',
                        'POST type-list' => 'type-list',
                        'POST profit-statistics' => 'profit-statistics',
                        'POST withdrawal-statistics' => 'withdrawal-statistics'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/cash-order',
                    'extraPatterns' => [
                        'POST withdraw-view' => 'withdraw-view',
                        'POST withdraw' => 'withdraw',
                        'POST withdraw-list' => 'withdraw-list',
                        'POST withdraw-total' => 'withdraw-total',
                        'POST is-set-paypassword' => 'is-set-paypassword'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/transaction',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST statistics' => 'statistics',
                        'POST detail' => 'detail',
                        'POST detail-list' => 'detail-list',
                        'POST total-list' => 'total-list',
                        'POST type-list' => 'type-list',
                        'POST statistics-total' => 'statistics-total'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/real',
                    'extraPatterns' => [
                        'POST bank-card-query' => 'bank-card-query',
                        'POST authentication-code' => 'authentication-code'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/message',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST find' => 'find',
                        'POST read' => 'read'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/news',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'POST find' => 'find'
                    ]
                ]
            ]
        ]
    ],
    'controllerMap' => [
        // 文件上传公共控制器
        'file' => [
            'class' => 'app\controllers\FileBaseController'
        ],
        'chang-jie-async' => [
            'class' => 'common\controllers\ChangJieAsyncController'
        ]
    ],
    'params' => $params
];
