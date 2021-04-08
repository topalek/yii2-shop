<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 08.10.14
 * Time: 13:46
 */

namespace common\modules\image\assets;


use yii\web\AssetBundle;

class UploadAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/image/assets';

    public $js = [
        'vendor/fileapi/FileAPI/FileAPI.min.js',
        'vendor/fileapi/FileAPI/FileAPI.exif.js',
        'vendor/fileapi/jquery.fileapi.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
