<?php
$current = dirname(__DIR__);
$parent = dirname($current);
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@attachment', dirname(dirname(__DIR__)) . '/attachment');
Yii::setAlias('@attachurl', '/attachment');
Yii::setAlias('@public', dirname(dirname(__DIR__)) . '/public');
Yii::setAlias('@app', dirname(dirname(__DIR__)) . '/app');
// 各自应用域名配置，如果没有配置应用独立域名请忽略
Yii::setAlias('@backendUrl', 'http://baidu.com');
Yii::setAlias('@frontendUrl', 'http://baidu.com');
Yii::setAlias('@wechatUrl', 'http://baidu.com');
Yii::setAlias('@apiUrl', 'http://baidu.com');
\yii::setAlias('trntv/debug/xhprof', $parent . '/common/library/yii2-debug-xhprof');