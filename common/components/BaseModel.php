<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 23.05.14
 * Time: 10:36
 */

namespace common\components;

use common\modules\seo\models\Seo;
use ReflectionClass;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\Cookie;

class BaseModel extends ActiveRecord
{

    /**
     * @return array
     */
    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;
    const DEFAULT_CACHE_DURATION = 86400;
    const DEFAULT_LANG = 'ru';
    public $autoCache = true;

    public static function getList($attributes = [])
    {
        $defaultAttributes = ['title_ru', 'id'];
        if (empty($attributes)) {
            $attributes = $defaultAttributes;
        }
        return static::find()->select($attributes)->orderBy('id DESC')->indexBy($attributes[1])->column();
    }

    /**
     * @param null   $table
     * @param string $field
     *
     * @return DbDependency
     */
    static function getDbDependency($table = null, $field = 'updated_at')
    {
        if ($table == null) {
            $table = self::tableName();
        }
        $dependency = new DbDependency();
        $dependency->sql = 'SELECT MAX(' . $field . ') FROM ' . $table;
        return $dependency;
    }

    static function findOneWithCache($id, $with_seo = true)
    {
        $cache_id = self::defaultCacheId($id);
        return self::findByCacheId($cache_id, $id, $with_seo);
    }

    public static function defaultCacheId($model_id)
    {
        return 'model_' . strtolower(self::getModelName()) . '_id_' . $model_id;
    }

    public static function getModelName($shortName = true)
    {
        $reflect = new ReflectionClass(static::class);
        return $shortName ? $reflect->getShortName() : $reflect->name;
    }

    public static function findByCacheId($cache_id, $model_id, $with_seo = true)
    {
        $model = Yii::$app->cache->get($cache_id);

        if (!$model) {
            $query = self::find();

            if ($with_seo) {
                $table = self::tableName();
                $query->with(['seo']);
                $query->where(["$table.id" => $model_id]);
            } else {
                $query->where(['id' => $model_id]);
            }
            $model = $query->limit(1)->one();

            Yii::$app->cache->set(self::defaultCacheId($model_id), $model, self::DEFAULT_CACHE_DURATION);
        }
        return $model;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value'      => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return string
     */
    public function modelUploadsPath()
    {
        $path = $this->moduleUploadsPath() . $this->id . '/';
        FileHelper::createDirectory($path);
        return $path;
    }

    /**
     * @return string
     */
    public static function moduleUploadsPath()
    {
        $path = str_replace('backend', 'frontend', Yii::$app->basePath) . "/web" . self::moduleUploadsDir() . '/';
        FileHelper::createDirectory($path);
        return $path;
    }

    /**
     * @return string
     */
    public static function moduleUploadsDir()
    {
        return '/uploads/' . strtolower(self::getModelName());
    }

    /**
     * @param $table
     *
     * @return bool|string
     */
    public function getMaxOrder($table = null)
    {
        if ($table == null) {
            $table = self::getModelName();
        }
        $maxOrder = (new Query())
            ->select('MAX(ordering) as maxOrder')
            ->from($table)
            ->scalar();
        return $maxOrder;
    }


    public function imgPreview()
    {
        $url = $this->modelUploadsUrl() . $this->main_img;
        if (strpos($this->main_img, 'http') !== false) {
            $url = $this->main_img;
        }
        return Html::img(
            isFrontendApp() ? $url : Yii::$app->params['frontendUrl'] . $url,
            ['class' => 'img-responsive', 'style' => 'max-height:170px']
        );
    }

    /**
     * @return string
     */
    public function modelUploadsUrl()
    {
        $basePath = '/uploads/';
        if (php_sapi_name() == "cli") // check if php run from console
        {
            $baseUrl = $basePath;
        } else {
            $baseUrl = Yii::$app->request->baseUrl;
        }
        return $baseUrl . $this->moduleUploadsDir() . '/' . $this->id . '/';
    }

    public function getStatusName()
    {
        return self::getStatusList()[$this->status];
    }

    static function getStatusList()
    {
        return [
            self::STATUS_PUBLISHED     => '????????????????????????',
            self::STATUS_NOT_PUBLISHED => '???? ??????????????????????',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeo()
    {
        return $this->hasOne(Seo::class, ['model_id' => 'id'])->where(['model_name' => self::getModelName()]);
    }

    /**
     * @param bool $withLangPrefix
     *
     * @return string
     */
    public function getSeoUrl($withLangPrefix = true)
    {
        if ($this->seo) {
            $langPrefix = (Yii::$app->language == self::DEFAULT_LANG) ? null : Yii::$app->language . '/';
            $url = '/';

            if ($withLangPrefix) {
                $url .= $langPrefix;
            }

            $url .= trim($this->seo->external_link, '/');

            return $url;
        } else {
            return null;
        }
    }

    public function deleteDefaultCache()
    {
        Yii::$app->cache->delete($this->defaultCacheId($this->id));
    }

    public function getCacheId()
    {
        return self::defaultCacheId($this->id);
    }

    public function updateViewCount()
    {
        $modelName = $this->getModelName();
        $viewData = Yii::$app->request->cookies->getValue('viewData', null);
        if (!$viewData) {
            $this->view_count += 1;
            $this->update(false, ['view_count']);
        } else {
            if (isset($viewData[$modelName])) {
                if (!in_array($this->id, $viewData[$modelName])) {
                    $this->view_count += 1;
                    $this->update(false, ['view_count']);
                }
            } else {
                $this->view_count += 1;
                $this->update(false, ['view_count']);
            }
        }
        $viewData[$modelName][] = $this->id;

        Yii::$app->response->cookies->add(
            new Cookie(
                [
                    'name'   => 'viewData',
                    'expire' => time() + 86400 * 365,
                    'value'  => $viewData,
                ]
            )
        );
    }

    public function getMlShortContent($lang = null, $attribute = 'description')
    {
        return getShortText(strip_tags($this->getMlAttribute($lang, $attribute)), 300);
    }

    /**
     * @param null   $lang
     * @param string $attribute
     *
     * @return mixed
     */
    protected function getMlAttribute($lang = null, $attribute)
    {
        if (!$lang) {
            $lang = Yii::$app->language;
        }

        if (php_sapi_name() == 'cli') {
            $lang = substr($lang, 0, -3);
        }

        $content = $attribute . '_' . $lang;
        $contentDefault = $attribute . '_ru';
        return $this->$content ?? $this->$contentDefault;
    }

    /**
     * @param null $lang
     * @param null $attribute
     *
     * @return mixed
     */
    public function getMlContent($lang = null, $attribute = 'description')
    {
        return $this->getMlAttribute($lang, $attribute);
    }

    public function getMlKeywords($lang = null, $attribute = 'keywords')
    {
        return $this->getMlAttribute($lang, $attribute);
    }

    public function getMainImg($options = [])
    {
        $defaultOptions = [
            'alt'   => $this->getMlTitle(),
            'class' => 'img-responsive main-image',
        ];
        $options = ArrayHelper::merge($options, $defaultOptions);
        $path = $this->getMainImgUrl();
        if ($path) {
            return Html::img($path, $options);
        }
        return '';
    }

    public function getMlTitle($lang = null, $attribute = 'title')
    {
        return $this->getMlAttribute($lang, $attribute);
    }

    public function getMainImgUrl()
    {
        if (!$this->main_img) {
            return '';
        }
        if (strpos($this->main_img, 'http') !== false) {
            return $this->main_img;
        }
        $url = $this::modelUploadsUrl() . $this->main_img;
        if (!isFrontendApp()) {
            $url = Yii::$app->params['frontendUrl'] . $url;
        }
        return $url;
    }
}
