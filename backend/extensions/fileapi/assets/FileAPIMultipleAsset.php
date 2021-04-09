<?php

namespace backend\extensions\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Пакет мульти-загрузки
 */
class FileAPIMultipleAsset extends AssetBundle
{
    public $sourcePath = '@app/extensions/fileapi/assets';
    public $css = [
        'css/multiple.css',
    ];
    public $depends = [
        'backend\extensions\fileapi\assets\FileAPIAsset',
    ];
}
