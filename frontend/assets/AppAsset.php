<?php
namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';

    public $baseUrl = '@web/resources';

    public $css = [
        'bower_components/bootstrap/dist/css/bootstrap.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
        'plugins/sweetalert/sweetalert.css', // 弹出框提示
        'plugins/fancybox/jquery.fancybox.min.css', // 图片查看
        'plugins/toastr/toastr.min.css', // 状态通知
        'dist/css/AdminLTE.min.css',
        'dist/css/rageframe.css',
        'dist/css/public.css',  // 公用样式
    ];

    public $js = [
        //'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'bower_components/fastclick/lib/fastclick.js',
        'plugins/layer/layer.js',
        'plugins/sweetalert/sweetalert.min.js',
        'plugins/fancybox/jquery.fancybox.min.js',
        'dist/js/adminlte.js',
        'dist/js/demo.js',
        'dist/js/template.js',
        'dist/js/rageframe.js',
        'dist/js/ueditor.all.min.js',
        'dist/js/public.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'frontend\assets\CompatibilityIEAsset',
        'frontend\assets\HeadJsAsset'
    ];
}
