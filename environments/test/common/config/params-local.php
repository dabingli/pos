<?php
return [
    'pos' => [
        'posPostUrl' => 'http://140.207.226.238:7101/channel/',
        'key' => '1q2w3e4r',
        'iv' => '1q2w3e4r',
        'version' => '1.0.0',
        'channel' => '3005',
        'notifKey' => 'aMahki5r'
    ],
    'changjie' => [
        'baseUrl' => 'https://pay.chanpay.com',
        'rsa_private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDQeXEOaX/kBxfWdvg46wF7LXcUByQx8q54tTx4oylZ+VR8v/jjYhFMEneZ8dXf1vprWCscnOh4jfuZYFkgeQxYpsmAojiJ4d7RkLTfqTfwLRNHYTIeCfTV2ndFxHrSz0YJme/eTv7GWU5wN8qOeDQark9fYo2H674tCVX9wWPYJQIDAQABAoGARXUgqMeXl5aRZ5/tpbEOpkaIlQCoG4gafxcLFbpuzY5XitS/DKsgzjKc7Ip5UoGin18Zxge1IG1VtU03hK0v4kNOs8d6+AjTA3iBtZWMIlFLoLiAzHnSCcc0jV35OuQYabN+5ZtdvQBP1ZyHnKSsc3pWlLs8hhKBVmBnr8HP5UECQQDztJhlMTkf9Hg86rH+3oRPiKvbRK/ms7T1WnKy7BAQMKjo1BGAAbxGrCYgVY+LRAp24BsV3mQMMkACeWJoLHi1AkEA2v3YL4lv+FDK2PnEJ811UmXsCEioP27/p1ta7G55PjTcHVQcUdPY+4n8YPQ50DZNRHWq3abt8+4qRw0wvs+3sQJADOZZTrntSSi6mJbftxr2K/OTDTc0jGSkxnv0KE5gh0rcFf7rsjflTGReXEXLJFcEqsgwBtdPummKg9cDA3qfJQJAUAaFUtHJjheQGPwk11q4bdT7DQfoG84nNHQo5M92FOpiKYGMG8bruvfwt0loOxMs50CMoRUYTZSR9Ib4cjIb8QJBALn+wza4JjiY0T3JgF35G2mVH/heIivrM6YIDmfV7ygISfiRnft/PeGP9ObmGEYRApkpT7+eQgQD8HfFVLEm/9c=
-----END RSA PRIVATE KEY-----',
        'rsa_public_key' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDPq3oXX5aFeBQGf3Ag/86zNu0VICXmkof85r+DDL46w3vHcTnkEWVbp9DaDurcF7DMctzJngO0u9OG1cb4mn+Pn/uNC1fp7S4JH4xtwST6jFgHtXcTG9uewWFYWKw/8b3zf4fXyRuI/2ekeLSstftqnMQdenVP7XCxMuEnnmM1RwIDAQAB
-----END PUBLIC KEY-----',
        'PartnerId' => '200001160096'
    ],
    'ocridcard' => [
        'AppCode' => '48aa14536599445883ac3078332f7b8a',
        'AppKey' => '25481213',
        'AppSecret' => 'e80821cb6475fdbd12d33019ca3ff578',
        'money' => '0.1' // 实名验证身份证一次收代理商的金额
    ],
    'bankCard' => [
        'AppCode' => '48aa14536599445883ac3078332f7b8a',
        'AppKey' => '25481213',
        'AppSecret' => 'e80821cb6475fdbd12d33019ca3ff578',
        'money' => '0.1' // 实名验证银行卡一次收代理商的金额
    ],
    'bankCardQuery' => [
        'AppCode' => '48aa14536599445883ac3078332f7b8a',
        'AppKey' => '25481213',
        'AppSecret' => 'e80821cb6475fdbd12d33019ca3ff578',
        'money' => '0.2' // 实名验证银行四要素一次收代理商的金额
    ],

    // 发送每条短信的金额
    'smsFee' => 0.5
];
