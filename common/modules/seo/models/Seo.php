<?php

namespace common\modules\seo\models;

use common\components\BaseModel;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "seo".
 *
 * @property integer $id
 * @property string  $title_uk
 * @property string  $title_ru
 * @property string  $title_en
 * @property string  $description_uk
 * @property string  $description_ru
 * @property string  $description_en
 * @property string  $keywords_uk
 * @property string  $keywords_ru
 * @property string  $keywords_en
 * @property string  $head_block
 * @property string  $external_link
 * @property string  $internal_link
 * @property string  $external_link_with_cat
 * @property integer $noindex
 * @property integer $nofollow
 * @property integer $in_sitemap
 * @property integer $is_canonical
 * @property string  $model_name
 * @property integer $model_id
 * @property integer $status
 * @property string  $updated_at
 */
class Seo extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo';
    }

    static function findByExternalLink($link)
    {
        $model = Yii::$app->cache->get('seo_' . $link);
        if (!$model) {
            $model = Seo::getDb()->cache(
                function () use ($link) {
                    return Seo::find()->where(['external_link' => $link])->one();
                },
                BaseModel::DEFAULT_CACHE_DURATION,
                BaseModel::getDbDependency('seo')
            );

            Yii::$app->cache->set('seo_' . $link, $model, BaseModel::DEFAULT_CACHE_DURATION);
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['head_block'], 'string'],
            [['external_link'], 'required'],
            [['noindex', 'nofollow', 'in_sitemap', 'is_canonical', 'model_id', 'status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [
                [
                    'title_uk',
                    'title_ru',
                    'title_en',
                    'description_uk',
                    'description_ru',
                    'description_en',
                    'keywords_uk',
                    'keywords_ru',
                    'keywords_en',
                    'external_link',
                    'internal_link',
                    'external_link_with_cat',
                    'model_name',
                ],
                'string',
                'max' => 255,
            ],
        ];
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'title_uk'               => 'Title страницы (uk)',
            'title_ru'               => 'Title страницы (ru)',
            'title_en'               => 'Title страницы (en)',
            'description_uk'         => 'Meta description (uk)',
            'description_ru'         => 'Meta description (ru)',
            'description_en'         => 'Meta description (en)',
            'keywords_uk'            => 'Meta keywords (uk)',
            'keywords_ru'            => 'Meta keywords (ru)',
            'keywords_en'            => 'Meta keywords (en)',
            'head_block'             => 'Блок в head',
            'external_link'          => 'Внешняя ссылка',
            'external_link_with_cat' => 'Внешняя ссылка з категорією',
            'internal_link'          => 'Системная ссылка',
            'noindex'                => 'Noindex',
            'nofollow'               => 'Nofollow',
            'in_sitemap'             => 'Добавить в sitemap',
            'is_canonical'           => 'Каноническа ссылка',
            'model_name'             => 'Название моделі',
            'model_id'               => 'Id моделі',
            'status'                 => 'Статус',
            'updated_at'             => 'Дата обновления',
        ];
    }

    public function beforeDelete()
    {
        Yii::$app->cache->delete('SeoFor' . $this->model_name . $this->model_id);
        Yii::$app->cache->delete('seo_' . $this->external_link);
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @param bool  $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->set('seo_' . $this->external_link, $this, 86400);
        self::findSeo($this->model_name, $this->model_id, true);
    }

    static function findSeo($modelName, $modelId, $updateCache = false)
    {
        $cacheKey = 'SeoFor' . $modelName . $modelId;
        $model = false;

        if (!$updateCache) {
            $model = Yii::$app->cache->get($cacheKey);
        }

        if ($model === false) {
            $model = Seo::findBySql("SELECT * FROM seo WHERE model_name='$modelName' AND model_id='$modelId'")->one();
            Yii::$app->cache->set($cacheKey, $model, BaseModel::DEFAULT_CACHE_DURATION);
        }

        if ($updateCache) {
            return null;
        }

        if ($model) {
            return $model;
        }
        return false;
    }

    public function getMlSeoData()
    {
        return [
            'title'       => $this->getMlTitle(),
            'description' => $this->getMlDescription(),
            'keywords'    => $this->getMlKeywords(),
        ];
    }

    public function getMlDescription()
    {
        return $this->getMlAttribute('description');
    }

    public function getMlKeywords()
    {
        return $this->getMlAttribute('keywords');
    }

}
