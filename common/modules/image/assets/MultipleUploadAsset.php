<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 08.10.14
 * Time: 17:33
 */

namespace common\modules\image\assets;


use yii\web\AssetBundle;

class MultipleUploadAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/image/assets';
    public $css = [
        'css/multiple.css',
    ];
    public $depends = [
        'common\modules\image\assets\UploadAsset',
    ];
}
