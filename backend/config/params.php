<?php
return [
    'adminEmail' => 'admin@example.com',
    'adminTitle' => '阿尔法狼●开店宝',
    'title' => '开店宝',
    /**
     * ------ 总管理员配置 ------ *
     */
    'adminAccount' => 1, // 系统管理员账号id
    'adminAcronym' => '首页',
    /**
     * ------ 备份配置配置 ------ *
     */
    'dataBackupPath' => Yii::getAlias('@common/backup'), // 数据库备份根路径
    'dataBackPartSize' => 20971520, // 数据库备份卷大小
    'dataBackCompress' => 1, // 压缩级别
    'dataBackCompressLevel' => 9, // 数据库备份文件压缩级别
    'dataBackLock' => 'backup.lock', // 数据库备份缓存文件名
    'isMobile' => false,
    /**
     * ------ 配置文本类型 ------ *
     */
    'configTypeList' => [
        'text' => "文本框",
        'password' => "密码框",
        'secretKeyText' => "密钥文本框",
        'textarea' => "文本域",
        'date' => "日期",
        'time' => "时间",
        'datetime' => "日期时间",
        'dropDownList' => "下拉文本框",
        'radioList' => "单选按钮",
        'checkboxList' => "复选框",
        'baiduUEditor' => "百度编辑器",
        'image' => "图片上传",
        'images' => "多图上传",
        'file' => "文件上传",
        'files' => "多文件上传"
    ],
    
    /**
     * ------ 开发者信息 ------ *
     */
    'exploitDeveloper' => '阿法狼开发技术团队',
    'exploitFullName' => '阿法狼开发技术团队',
    /**
     * 不需要验证的路由全称
     *
     * 注意: 前面以绝对路径/为开头
     */
    'noAuthRoute' => [
        '/main/index', // 系统主页
        '/main/system', // 系统首页
        '/ueditor/index', 
        '/menu-provinces/index' 
    ]
];
