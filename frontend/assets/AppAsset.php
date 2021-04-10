<?php

namespace frontend\assets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css',
    ];
    public $js = [
        'js/lozad.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        BootstrapPluginAsset::class,
    ];
}
