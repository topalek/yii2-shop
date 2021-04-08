<?php

namespace common\modules\seo\controllers;

use common\modules\seo\models\Seo;
use samdark\sitemap\Sitemap;
use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionSiteMap()
    {
        $items = Seo::find()->where(['in_sitemap' => 1])->count();
        $countFiles = ceil($items / 20000);
        $siteName = 'http://' . $_SERVER['HTTP_HOST'];

        // write parent file
        $fp = fopen(Yii::$app->basePath . '/../sitemap.xml', 'w');
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($fp, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

        for ($i = 1; $i <= $countFiles; $i++) {
            fwrite($fp, '<sitemap>' . "\n");
            fwrite($fp, '<loc>' . $siteName . '/sitemap' . $i . '.xml</loc>' . "\n");
            fwrite($fp, '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n");
            fwrite($fp, '</sitemap>' . "\n");
        }

        fwrite($fp, '</sitemapindex>' . "\n");
        fclose($fp);

        for ($i = 1; $i <= $countFiles; $i++) {
            $fp = fopen(Yii::$app->basePath . '/../sitemap' . $i . '.xml', 'w');
            fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            fwrite($fp, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

            if ($i == 1) {
                fwrite(
                    $fp,
                    "<url><loc>$siteName/</loc><lastmod>" . date(
                        'Y-m-d'
                    ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                );
                fwrite(
                    $fp,
                    "<url><loc>$siteName/ru/</loc><lastmod>" . date(
                        'Y-m-d'
                    ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                );
                fwrite(
                    $fp,
                    "<url><loc>$siteName/en/</loc><lastmod>" . date(
                        'Y-m-d'
                    ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                );
            }

            $items = Seo::find()->where(['in_sitemap' => 1])->limit(20000)->offset(20000 * ($i - 1))->all();

            if ($items) {
                foreach ($items as $key => $item) {
                    if ($item->external_link != '/') {
                        fwrite(
                            $fp,
                            "<url><loc>$siteName/{$item->external_link}</loc><lastmod>" . date(
                                'Y-m-d',
                                strtotime($item->updated_at)
                            ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                        );
                        fwrite(
                            $fp,
                            "<url><loc>$siteName/ru/{$item->external_link}</loc><lastmod>" . date(
                                'Y-m-d',
                                strtotime($item->updated_at)
                            ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                        );
                        fwrite(
                            $fp,
                            "<url><loc>$siteName/en/{$item->external_link}</loc><lastmod>" . date(
                                'Y-m-d',
                                strtotime($item->updated_at)
                            ) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>"
                        );
                    }
                }
            }

            fwrite($fp, '</urlset>');
            fclose($fp);
        }

        return $this->renderFile(Yii::$app->basePath . '/../sitemap.xml');
    }
}
