<?php

use common\components\DynamicImgThumbMaker;

if (strpos($_SERVER['REQUEST_URI'], '/thumbs/') !== false) {
    try {
        require_once(dirname(dirname(__DIR__)) . '/common/components/DynamicImgThumbMaker.php');
        new DynamicImgThumbMaker();
        die;
    } catch (Exception $exception) {
        print_r($exception);
        die;
    }
}
date_default_timezone_set('Europe/Kiev');

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . '/frontend/web/uploads');

/**
 * @param      $data
 * @param int  $num
 * @param bool $highlight
 */
function dd($data, $num = 10, $highlight = true)
{
    dump($data, $num, $highlight);
    die();
}

/**
 * @param      $data
 * @param int  $num
 * @param bool $highlight
 */
function dump($data, $num = 10, $highlight = true)
{
    \yii\helpers\VarDumper::dump($data, $num, $highlight);
}

function getImgSize($src)
{
    return @getimagesize($src);
}

/**
 * @return bool
 */
function isGuest(): bool
{
    return Yii::$app->user->isGuest;
}

function currUser(): \yii\web\IdentityInterface
{
    return Yii::$app->user->identity;
}

function currUserId(): ?int
{
    return Yii::$app->user->getId();
}

/**
 * @return string
 */
function uploadsDirName()
{
    return 'uploads';
}

/**
 * @return bool|string
 */
function getBaseUploadsUrl()
{
    return '/' . uploadsDirName() . '/';
}


/**
 * @param      $imageUrl
 * @param      $width  int|string 'auto'
 * @param      $height null|int|string 'auto'
 * @param bool $crop
 *
 * @return string
 */
function dynamicImageUrl($imageUrl, $width, $height = null, $crop = false): string
{
    if ($height == null) {
        $height = $width;
    }
    $imageUrlParts = explode('/', $imageUrl);
    $data = 's' . $width . '_' . $height;
    //    if(Params::useRemoteUploads()){
    //        $data .= '__r';
    //    }
    if ($crop) {
        $data .= '__c';
    }
    $last = array_pop($imageUrlParts);
    $imageUrlParts[] = 'thumbs';
    $imageUrlParts[] = $data;
    $imageUrlParts[] = $last;
    $imageUrl = implode('/', $imageUrlParts);
    return $imageUrl;
}
