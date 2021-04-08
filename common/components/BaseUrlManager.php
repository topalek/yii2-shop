<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 27.05.14
 * Time: 16:13
 */

namespace common\components;

use app\modules\seo\models\Seo;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\UrlManager;

class BaseUrlManager extends UrlManager
{
    const DEFAULT_LANG = BaseModel::DEFAULT_LANG;

    public static function getUrlWithoutLangPrefix()
    {
        $prefix = self::getLangPrefix();
        if ($prefix) {
            return str_replace($prefix . '/', '', Yii::$app->request->url);
        } else {
            return Yii::$app->request->url;
        }
    }

    public static function getLangPrefix()
    {
        $langPrefix = substr(Yii::$app->request->url, 0, 3);
        $langPrefix = ltrim($langPrefix, '/');
        if (in_array($langPrefix, array_keys(self::langList()))) {
            return $langPrefix;
        }
        return false;
    }

    public function createAbsoluteUrl($params, $scheme = null, $withLangPrefix = true)
    {
        $params = (array)$params;
        $url = $this->createUrl($params);

        if (strpos($url, '://') === false) {
            $hostInfo = $this->getHostInfo();
            if (strpos($url, '//') === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }

        if (!$withLangPrefix && Yii::$app->language != self::DEFAULT_LANG) {
            $url = str_replace(Yii::$app->language . '/', '', $url);
        }

        return Url::ensureScheme($url, $scheme);
    }

    public function createUrl($params)
    {
        $url = parent::createUrl($params);

        if (Yii::$app->language != self::DEFAULT_LANG) {
            return '/' . Yii::$app->language . $url;
        }

        return $url;
    }

    public function parseRequest($request)
    {
        $pathInfo = $request->getPathInfo();

        if (self::checkForExclusion($pathInfo)) {
            $langArray = array_keys(self::langList());
            $langParam = substr($pathInfo, 0, 2);
            $hasLangParam = in_array($langParam, $langArray);
            $urlWithoutLangParam = str_replace($langParam . '/', '', $pathInfo);
            if (strpos($pathInfo, 'sitemap') !== false && $hasLangParam) {
                Yii::$app->response->redirect('/' . $urlWithoutLangParam, 301);
                Yii::$app->end();
            }

            if ($pathInfo == '') {
                $pathInfo = '/';
            }

            if (!Yii::$app->request->isAjax) {
                if ($pathInfo == '/' || !$hasLangParam) {
                    Yii::$app->language = self::DEFAULT_LANG;
                } elseif ($hasLangParam) {
                    Yii::$app->language = $langParam;
                }
            }

            try {
                if (Yii::$app->language != self::DEFAULT_LANG && $hasLangParam) {
                    $pathInfo = $urlWithoutLangParam;
                }
                $seo = Seo::findByExternalLink($pathInfo);
            } catch (Exception $e) {
                $seo = null;
            }

            if ($seo != null) {
                return [$seo->internal_link, ['id' => $seo->model_id]];
            }

            $request->setPathInfo(str_replace(Yii::$app->language . '/', '', $pathInfo));
        }

        return parent::parseRequest($request);
    }

    /**
     * Перевірка урл на виключення
     *
     * @param $url
     *
     * @return bool
     */
    public static function checkForExclusion($url)
    {
        $exclusion = ['debug', 'imperavi-upload', 'admin'];
        foreach ($exclusion as $item) {
            if (strpos($url, $item) !== false) {
                return false;
            }
        }
        return true;
    }

    public static function langList()
    {
        return [
            'ru' => 'Русский',
            'uk' => 'Українська',
            'en' => 'English',
        ];
    }
}
