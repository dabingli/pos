<?php
$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => ''
        ],
        'authenticator' => [
            'class' => 'app\models\Authenticator',
            'authTimeout' => false
        ]
    ]
];

return $config;
