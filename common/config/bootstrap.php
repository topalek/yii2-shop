<?php

const BACKEND_APP_ID = 'app-backend';
const FRONTEND_APP_ID = 'app-frontend';
const CONSOLE_APP_ID = 'app-console';

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

function lazyLoadImgUrl()
{
    return '/img/loader.gif';
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
function dynamicImageUrl(
    $imageUrl,
    $width,
    $height = null,
    $crop = false,
    $remoteMode = false,
    $waterMark = false
): string {
    if (!$imageUrl) {
        return '';
    }
    if ($height == null) {
        $height = $width;
    }

    $data = 's' . $width . '_' . $height;
    if ($crop) {
        $data .= '__c';
    }

    if ($remoteMode) {
        $data .= '__r';
    }

    if ($waterMark) {
        $data .= '__wm';
    }

    if ($remoteMode) {
        $last = base64_encode($imageUrl) . '.webp';
        $last = preg_replace_callback(
            '/([A-Z])/',
            function ($symbol) {
                return '-' . strtolower($symbol[0]);
            },
            $last
        );
        $imageUrlParts = [''];
    } else {
        $imageUrlParts = explode('/', $imageUrl);
        $last = array_pop($imageUrlParts);
    }

    $imageUrlParts[] = 'thumbs';
    $imageUrlParts[] = $data;
    $imageUrlParts[] = $last;
    $imageUrl = implode('/', $imageUrlParts);
    return $imageUrl;
}

function getShortText($text, $width, $points = false)
{
    $text = strip_tags($text);
    $parts = preg_split('/([\s\n\r]+)/', $text, null, PREG_SPLIT_DELIM_CAPTURE);
    $parts_count = count($parts);
    $length = 0;
    $last_part = 0;
    for (; $last_part < $parts_count; ++$last_part) {
        $length += strlen($parts[$last_part]);
        if ($length > $width) {
            break;
        }
    }
    $result = implode(array_slice($parts, 0, $last_part));
    if ($points) {
        if ($length > $width) {
            $result = $result . '...';
        }
    }
    return $result;
}

function searchHighlighter($text, $word, $trim = false, $length = 0, $points = false)
{
    if ($trim) {
        $fragment = getShortText($text, $length, $points);
    } else {
        $fragment = $text;
    }
    $highlighted = str_ireplace($word, '<mark>' . $word . '</mark>', $fragment);
    return $highlighted;
}

function isBackendApp(): bool
{
    return Yii::$app->id == BACKEND_APP_ID;
}

function isFrontendApp(): bool
{
    return Yii::$app->id == FRONTEND_APP_ID;
}

function isConsoleApp(): bool
{
    return Yii::$app->id == CONSOLE_APP_ID;
}

function moveElement(&$array, $from, $to)
{
    $out = array_splice($array, $from, 1);
    array_splice($array, $to, 0, $out);
}

/**
 * @param        $number
 * @param string $decPoint
 * @param string $thousandsSep
 * @param int    $decCount
 *
 * @return string
 */
function asMoney($number, $decPoint = ',', $thousandsSep = ' ', $decCount = 0)
{
    if ($decCount == 0) {
        $number = round($number, 0);
    }
    return number_format($number, $decCount, $decPoint, $thousandsSep);
}
