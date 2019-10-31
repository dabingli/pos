<?php
namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class HeadJsAsset
 * @package backend\assets
 * @author jianyan74 <751393839@qq.com>
 */
class HeadJsAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web/resources';

    public $js = [
        'bower_components/jquery/dist/jquery.min.js',
        'plugins/toastr/toastr.min.js',
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'bootstrap/dist/bootstrap-table.min.js',
        'bootstrap/dist/locale/bootstrap-table-zh-CN.min.js'
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
