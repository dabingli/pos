<?php
return [
    'adminEmail' => 'admin@example.com',
    /**
     * ------ 日志记录 ------ *
     */
    'user.log' => true,
    'user.log.level' => [
        'error'
    ], // 级别 ['info', 'warning', 'error']
    'user.log.noPostData' => [ // 安全考虑,不接收Post存储到日志的路由
        
    ],
    'user.log.except.code' => [] // 不记录的code
];
