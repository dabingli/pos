<?php
return [
    'title' => '开店宝助手',
    'adminTitle' => '开店宝管理后台',
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'httpCacheVersion' => '1.0.0',
    /**
     * ------ 日志记录 ------ *
     */
    'user.log' => true,
    'user.log.level' => [
        'error'
    ], // 级别 ['info', 'warning', 'error']
    'user.log.noPostData' => [ // 安全考虑,不接收Post存储到日志的路由

    ],
    'user.log.except.code' => [], // 不记录的code
                                  // 全局上传配置
    'uploadConfig' => [
        // 图片
        'images' => [
            'originalName' => false, // 是否保留原名
            'fullPath' => true, // 是否开启返回完整的文件路径
            'takeOverUrl' => '', // 配置后，接管所有的上传地址
            'drive' => 'oss', // 默认本地 可修改 qiniu/oss 上传
            'maxSize' => 1024 * 1024 * 4, // 图片最大上传大小,默认2M
            'extensions' => [
                "png",
                "jpg",
                "jpeg",
                "gif",
                "bmp"
            ], // 可上传图片后缀不填写即为不限
            'path' => 'images/', // 图片创建路径
            'subName' => 'Y/m/d', // 图片上传子目录规则
            'prefix' => 'image_', // 图片名称前缀
            'compress' => false, // 是否开启压缩
            'compressibility' => [ // 100不压缩 值越大越清晰 注意先后顺序
                1024 * 100 => 100, // 0 - 100k 内不压缩
                1024 * 1024 => 30, // 100k - 1M 区间压缩质量到30
                1024 * 1024 * 2 => 20, // 1M - 2M 区间压缩质量到20
                1024 * 1024 * 1024 => 10 // 2M - 1G 区间压缩质量到20
            ]
        ],
        // 视频
        'videos' => [
            'originalName' => true, // 是否保留原名
            'fullPath' => true, // 是否开启返回完整的文件路径
            'takeOverUrl' => '', // 配置后，接管所有的上传地址
            'drive' => 'oss', // 默认本地 可修改 qiniu/oss 上传
            'maxSize' => 1024 * 1024 * 10, // 最大上传大小,默认10M
            'extensions' => [
                'mp4'
            ], // 可上传文件后缀不填写即为不限
            'path' => 'videos/', // 创建路径
            'subName' => 'Y/m/d', // 上传子目录规则
            'prefix' => 'video_' // 名称前缀
        ],
        // 语音
        'voices' => [
            'originalName' => true, // 是否保留原名
            'fullPath' => true, // 是否开启返回完整的文件路径
            'takeOverUrl' => '', // 配置后，接管所有的上传地址
            'drive' => 'oss', // 默认本地 可修改 qiniu/oss 上传
            'maxSize' => 1024 * 1024 * 50, // 最大上传大小,默认50M
            'extensions' => [
                'amr',
                'mp3'
            ], // 可上传文件后缀不填写即为不限
            'path' => 'voices/', // 创建路径
            'subName' => 'Y/m/d', // 上传子目录规则
            'prefix' => 'voice_' // 名称前缀
        ],
        // 文件
        'files' => [
            'originalName' => true, // 是否保留原名
            'fullPath' => true, // 是否开启返回完整的文件路径
            'takeOverUrl' => '', // 配置后，接管所有的上传地址
            'drive' => 'oss', // 默认本地 可修改 qiniu/oss 上传
            'maxSize' => 1024 * 1024 * 50, // 最大上传大小,默认50M
            'extensions' => [], // 可上传文件后缀不填写即为不限
            'path' => 'files/', // 创建路径
            'subName' => 'Y/m/d', // 上传子目录规则
            'prefix' => 'file_' // 名称前缀
        ],
        // 缩略图
        'thumb' => [
            'path' => 'thumb/' // 图片创建路径
        ]
    ]
];
