<?php

use Imagine\Image\Box;
use Imagine\Image\Point;
use yii\helpers\FileHelper;
use yii\imagine\Image as ImageHelper;

function cmp($a, $b)
{
    if ($a['ordering'] == $b['ordering']) {
        return 0;
    }
    return ($a['ordering'] < $b['ordering']) ? -1 : 1;
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

function dump($data, $num = 10, $highlight = true)
{
    yii\helpers\VarDumper::dump($data, $num, $highlight);
}

function dd($data, $num = 10, $highlight = true)
{
    dump($data, $num, $highlight);
    die;
}

/**
 * @param      $original_path
 * @param      $original_file_name
 * @param      $resize_file_path
 * @param      $width
 * @param      $height
 * @param bool $web_path
 *
 * @return bool|string
 */

function resizeImage($original_path, $original_file_name, $resize_file_path, $width, $height, $web_path = false)
{
    FileHelper::createDirectory($resize_file_path);

    $name = $width . '_' . $height . '_' . $original_file_name;

    $originalFullName = DIRECTORY_SEPARATOR . trim($original_path, '/') . DIRECTORY_SEPARATOR . $original_file_name;

    if (!file_exists($originalFullName)) {
        return false;
    }

    $thumbFullName = DIRECTORY_SEPARATOR . trim($resize_file_path, '/') . DIRECTORY_SEPARATOR . $name;


    if (!file_exists($thumbFullName)) {
        $originalImgInfo = getimagesize($originalFullName);
        $originalWidth = $originalImgInfo[0];
        $originalHeight = $originalImgInfo[1];

        ImageHelper::getImagine()->open($originalFullName)->thumbnail(
            new Box($width + $width / 2, $height + $height / 2)
        )->save($thumbFullName);

        $imgInfo = getimagesize($thumbFullName);
        $imgWidth = $imgInfo[0];
        $imgHeight = $imgInfo[1];


        if ($imgWidth < $width) {
            $newWidth = $width + ($width - $imgWidth);
            $newHeight = round($width / $originalWidth * $originalHeight);
            ImageHelper::getImagine()->open($originalFullName)->thumbnail(new Box($newWidth, $newHeight))->save(
                $thumbFullName,
                ['quality' => 100]
            );
            $imgInfo = getimagesize($thumbFullName);
            $imgWidth = $imgInfo[0];
            $imgHeight = $imgInfo[1];
        }

        if ($imgHeight < $height) {
            $newWidth = round($height / $originalHeight * $originalWidth);
            $newHeight = $height + ($height - $imgHeight);
            ImageHelper::getImagine()->open($originalFullName)->thumbnail(new Box($newWidth, $newHeight))->save(
                $thumbFullName,
                ['quality' => 100]
            );
            $imgInfo = getimagesize($thumbFullName);
            $imgWidth = $imgInfo[0];
            $imgHeight = $imgInfo[1];
        }

        if ($imgWidth > $width || $imgHeight > $height) {
            $startX = 0;
            $startY = 0;
            if ($imgWidth > $width) {
                $startX = ceil($imgWidth - $width) / 2;
            }
            if ($imgWidth > $height) {
                $startY = ceil($imgHeight - $height) / 2;
            }
            ImageHelper::getImagine()->open($thumbFullName)->crop(
                new Point($startX, $startY),
                new Box($width, $height)
            )->save($thumbFullName, ['quality' => 100]);
        }
    }

    if ($web_path) {
        return DIRECTORY_SEPARATOR . trim($web_path, '/') . DIRECTORY_SEPARATOR . $name;
    }

    return $name;
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

/**
 * @param      $imageUrl
 * @param      $width  int|string 'auto'
 * @param      $height null|int|string 'auto'
 * @param bool $crop
 * @param bool $remoteMode
 *
 * @return string
 */
function makeDynamicImageThumbUrl($imageUrl, $width, $height = null, $crop = false, $remoteMode = false): string
{
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
