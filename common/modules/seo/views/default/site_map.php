<?php

/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 04.02.16
 * Time: 12:11
 */
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

echo '
<url>
<loc>http://' . $_SERVER['HTTP_HOST'] . '</loc>
<changefreq>daily</changefreq>
<priority>1.0</priority>
</url>
';

if ($items) {
    foreach ($items as $item) {
        $url = $item->external_link;
        if ($url == null) {
            $url = $item->internal_link;
        }
        if ($url != '/') {
            echo '
<url>
<loc>http://' . $_SERVER['HTTP_HOST'] . '/' . $url . '</loc>
<lastmod>' . date(DATE_W3C, strtotime($item->updated_at)) . '</lastmod>
<changefreq>always</changefreq>
<priority>0.8</priority>
</url>';
        }
    }
}

echo "</urlset>";
