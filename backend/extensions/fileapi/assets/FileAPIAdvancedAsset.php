<?php

namespace backend\extensions\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Пакет продвинутой загрузки.
 */
class FileAPIAdvancedAsset extends AssetBundle
{
    public $sourcePath = '@app/extensions/fileapi/assets';
    public $css = [
        'css/advanced.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'backend\extensions\fileapi\assets\FileAPIAsset',
    ];
}
