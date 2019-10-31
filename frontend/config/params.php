<?php
return [
    'adminEmail' => 'admin@example.com',
    /**
     * 不需要验证的路由全称
     *
     * 注意: 前面以绝对路径/为开头
     */
    'noAuthRoute' => [
        '/main/index', // 系统主页
        '/main/system', // 系统首页
        '/ueditor/index', // 百度编辑器配置及上传
        '/menu-provinces/index'
    ],
    'dataBackupPath' => Yii::getAlias('@common/backup'), // 数据库备份根路径
    'dataBackLock' => 'backup.lock', // 数据库备份缓存文件名
    'isMobile' => false,
    // 'adminAccount' => 8,
    'adminAcronym' => '首页'
];
