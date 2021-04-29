<?php

class DynamicImgThumbMaker
{
    public const JPG_QUALITY = 100;
    public const WEBP_QUALITY = 100;

    private $marketUri;
    private $localPath;
    private $imgStr;
    private $imgResource;
    private bool $needJpg = false;
    private bool $needFinalResize = false;
    private bool $cropMode = false;
    private bool $remoteMode = false;
    private bool $withWaterMark = false;

    private $currentSizeRule = null;

    function __construct()
    {
        if (!isset($_SERVER['HTTP_ACCEPT']) || stripos($_SERVER['HTTP_ACCEPT'], 'webp') === false) {
            $this->needJpg = true;
        }

        $this->marketUri = preg_replace('#(^/+|\?.*$)#', '', $_SERVER['REQUEST_URI']); //|\.\./|\.(php|yml)

        $date = new DateTime();
        $date->modify('- 2 month');
        header('Last-Modified: ' . $date->format('D, d M Y H:i:s T'));
        unset($date);

        preg_match('/thumbs\/(.+?)\//', $this->marketUri, $matches);
        if (!empty($matches)) {
            $this->marketUri = str_replace($matches[0], '/', urldecode($this->marketUri));
            $params = $matches[1];
            $params = explode('__', $params);
            $sizeParams = $params[0];
            unset($params[0]);
            $sizeParams = str_replace('s', '', $sizeParams);
            $sizeParams = explode('_', $sizeParams);
            $this->currentSizeRule['finalWidth'] = $sizeParams[0];
            $this->currentSizeRule['finalHeight'] = $sizeParams[1];
            $this->needFinalResize = true;
            foreach ($params as $param) {
                switch ($param) {
                    case 'c':
                        $this->cropMode = true;
                        break;
                    case 'r':
                        $this->remoteMode = true;
                        preg_match('/.*?\/(.+?)[.]webp/', $this->marketUri, $match);
                        $match = preg_replace_callback(
                            '/([-][a-z])/',
                            function ($s) {
                                return strtoupper(str_replace('-', '', $s[0]));
                            },
                            $match[1]
                        );
                        $this->marketUri = base64_decode($match);
                        unset($match);
                        break;
                    case 'wm':
                        $this->withWaterMark = true;
                        break;
                }
            }

            unset($matches, $params, $sizeParams);
        } else {
            $this->marketUri = str_replace('thumbs/', '', $this->marketUri);
        }

        $this->localPath = $this->marketUri;

        if (!$this->remoteMode && !is_file($this->localPath)) {
            $this->sendAnswerCode(404);
        }
        $this->imgStr = file_get_contents($this->localPath);

        //        if ($this->needFinalResize === true) {
        //            $this->resizeImage($this->currentSizeRule['finalWidth'], $this->currentSizeRule['finalHeight']);
        //        }
        if ($this->needJpg) {
            if (($imgInfo = getimagesizefromstring($this->imgStr)) === false) {
                $this->sendAnswerCode(503, "Not an image");
            }

            if (!$this->needFinalResize) {
                header("Content-Type: {$imgInfo['mime']}");
                header('Content-Length: ' . strlen($this->imgStr));
                echo $this->imgStr;
                die;
            }
            if (!$this->needFinalResize
                || $this->resizeImage(
                    $this->currentSizeRule['finalWidth'],
                    $this->currentSizeRule['finalHeight']
                ) !== true) {
                $this->createImageFromCurrentString($imgInfo[2]);
            }
            header("Content-Type: image/jpeg");

            if (imagejpeg($this->imgResource, null, self::JPG_QUALITY) === false) {
                $this->sendAnswerCode(503, "Img processing error 2");
            }
        } else {
            header("Content-Type: image/webp");
            if ($this->needFinalResize === true) {
                if ($this->resizeImage(
                        $this->currentSizeRule['finalWidth'],
                        $this->currentSizeRule['finalHeight']
                    ) !== true) {
                    $this->createImageFromCurrentString();
                }

                imagewebp($this->imgResource, null, self::WEBP_QUALITY);
                die;
            }
            header('Content-Length: ' . strlen($this->imgStr));
        }
        echo $this->imgStr;
    }

    function sendAnswerCode($code, $message = null)
    {
        switch ($code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
            case 403:
                header("HTTP/1.0 403 Forbidden");
                break;
            case 503:
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                break;
            default:
                header("HTTP/1.1 $code");
        }
        if ($message !== null) {
            header("X-dbg: $message");
        }
        $img = @file_get_contents("404.jpg");
        if ($img) {
            header("Content-Type: image/jpeg");
            echo $img;
        }
        die();
    }

    function resizeImage($targetWidth, $targetHeight)
    {
        $requestedWidth = $targetWidth;
        $requestedHeight = $targetHeight;
        $imgInfo = getimagesizefromstring($this->imgStr);
        $srcWidth = $imgInfo[0];
        $srcHeight = $imgInfo[1];
        $dstX = $srcX = 0;
        $dstY = $srcY = 0;

        if ($targetWidth == 'auto' || $targetHeight == 'auto') {
            $proportion = $srcWidth / $srcHeight;
            if ($targetWidth == 'auto') {
                $targetWidth = ceil($targetHeight * $proportion);
                $requestedWidth = $targetWidth;
            } else {
                $targetHeight = ceil($targetWidth / $proportion);
                $requestedHeight = $targetHeight;
            }
        } else {
            $scaleHeight = $targetHeight / $srcHeight;
            $scaleWidth = $targetWidth / $srcWidth;

            if ($scaleWidth >= $scaleHeight) { // resize by height
                if ($scaleHeight >= 1) {
                    return;
                }
                if ($this->cropMode) {
                    $proportionalHeight = $scaleWidth * $srcHeight;
                    $extraPixels = ($proportionalHeight - $targetHeight) * ($srcHeight / $proportionalHeight);
                    $srcY = ceil($extraPixels / 2);
                    $srcHeight = $srcHeight - $extraPixels;
                } else {
                    $targetWidth = ceil($scaleHeight * $srcWidth);
                }
            } else { // resize by width
                if ($scaleWidth >= 1) {
                    return;
                }
                if ($this->cropMode) {
                    $proportionalWidth = $scaleHeight * $srcWidth;
                    $extraPixels = ($proportionalWidth - $targetWidth) * ($srcWidth / $proportionalWidth);
                    $srcX = ceil($extraPixels / 2);
                    $srcWidth = $srcWidth - $extraPixels;
                } else {
                    $targetHeight = ceil($scaleWidth * $srcHeight);
                }
            }
        }

        if ($requestedWidth > $targetWidth) {
            $dstX = ($requestedWidth - $targetWidth) / 2;
        }
        if ($requestedHeight > $targetHeight) {
            $dstY = ($requestedHeight - $targetHeight) / 2;
        }

        $this->createImageFromCurrentString($imgInfo[2]);
        $dst = imagecreatetruecolor($requestedWidth, $requestedHeight);
        $bg = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $bg);
        //        imagealphablending($dst, false);
        //        imagesavealpha($dst, true);
        imagecopyresampled(
            $dst,
            $this->imgResource,
            $dstX,
            $dstY,
            $srcX,
            $srcY,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );
        $this->imgResource = $dst;
        if ($this->withWaterMark) {
            $this->imgResource = $this->addWaterMark($this->imgResource, $requestedWidth, $requestedHeight);
        }
        return true;
    }

    function createImageFromCurrentString($imageType = null)
    {
        if ($imageType === null) {
            $imgInfo = getimagesizefromstring($this->imgStr);
            if (!$imgInfo) {
                $this->sendAnswerCode(503, "Img processing error - 0");
            }
            $imageType = $imgInfo[2];
        }
        if ($imageType === IMAGETYPE_WEBP) {
            //$currentImg = imagecreatefromstring($this->imgStr); // метод пока не работает с webp, поэтому через временный файл
            $tmpMemPath = '/tmp/i' . getmypid();
            file_put_contents($tmpMemPath, $this->imgStr);
            $this->imgResource = imagecreatefromwebp($tmpMemPath);
            @unlink($tmpMemPath);
        } else {
            $this->imgResource = imagecreatefromstring($this->imgStr);
        }

        if ($this->imgResource === false) {
            error_log("Create image from string error - {$this->marketUri}");
            $this->sendAnswerCode(503, "Img processing error");
        }
    }

    function addWaterMark($source, $sourceWidth, $sourceHeight)
    {
        $waterMark = imagecreatefrompng(__DIR__ . '/../../frontend/web/uploads/water_mark.png');
        $waterMarkWidth = imagesx($waterMark);
        $waterMarkHeight = imagesy($waterMark);

        $proportion = $waterMarkWidth / $waterMarkHeight;
        $newWatermarkWidth = 0.15 * $sourceWidth; //6 percents from source width
        $newWatermarkHeight = ceil($newWatermarkWidth / $proportion);
        imagecopyresampled(
            $source,
            $waterMark,
            5,
            $sourceHeight - $newWatermarkHeight - 5,
            0,
            0,
            $newWatermarkWidth,
            $newWatermarkHeight,
            $waterMarkWidth,
            $waterMarkHeight
        );

        //        if ($waterMarkWidth > $sourceWidth || $waterMarkHeight > $sourceHeight) {
        //            $proportion = $waterMarkWidth / $waterMarkHeight;
        //            if ($waterMarkWidth > $sourceWidth) {
        //                $newWatermarkWidth = $sourceWidth;
        //                $newWatermarkHeight = ceil($newWatermarkWidth / $proportion);
        //            } else {
        //                $newWatermarkHeight = $sourceHeight;
        //                $newWatermarkWidth = ceil($newWatermarkHeight * $proportion);
        //            }
        //            imagecopyresampled(
        //                $source,
        //                $waterMark,
        //                $sourceWidth - $newWatermarkWidth,
        //                $sourceHeight - $newWatermarkHeight,
        //                0,
        //                0,
        //                $newWatermarkWidth,
        //                $newWatermarkHeight,
        //                $waterMarkWidth,
        //                $waterMarkHeight
        //            );
        //        } else {
        //            imagecopy(
        //                $source,
        //                $waterMark,
        //                $sourceWidth - $waterMarkWidth,
        //                $sourceHeight - $waterMarkHeight,
        //                0,
        //                0,
        //                $waterMarkWidth,
        //                $waterMarkHeight
        //            );
        //        }

        return $source;
    }
}


