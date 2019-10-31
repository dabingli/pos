<?php
namespace common\helpers;

use yii;

/**
 * cdn帮助类
 *
 * @author Administrator
 *        
 */
class CdnHelper
{

    public static function Url($res)
    {
        $cdn = Yii::$app->debris->config('system_cdn');
        $cdn = rtrim($cdn, '/');
        return $cdn . '/' . $res;
    }
}