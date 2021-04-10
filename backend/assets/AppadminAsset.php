<?php

namespace backend\assets;

use dmstr\web\AdminLteAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main backend application asset bundle.
 */
class AppadminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i',
        'css/admin.css',
    ];
    public $js = [
        "vendor/jquery-easing/jquery.easing.min.js",
        "js/admin.js",
        //        "vendor/chart.js/Chart.min.js",
        //        "js/demo/chart-area-demo.js",
        //        "js/demo/chart-pie-demo.js",
    ];
    public $depends = [
        'yii\web\YiiAsset',
        JqueryAsset::class,
        BootstrapAsset::class,
        AdminLteAsset::class,
    ];
}
