<?php

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
