<?php

namespace backend\extensions\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Пакет одиночной загрузки
 */
class FileAPISingleAsset extends AssetBundle
{
    public $sourcePath = '@app/extensions/fileapi/assets';
    public $css = [
        'css/single.css',
    ];
    public $depends = [
        'backend\extensions\fileapi\assets\FileAPIAsset',
    ];
}
