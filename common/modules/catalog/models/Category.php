<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use common\extensions\fileapi\behaviors\UploadBehavior;
use common\modules\search\behaviors\SearchBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "category".
 *
 * @property integer            $id
 * @property string             $title_uk
 * @property string             $title_ru
 * @property string             $title_en
 * @property string             $description_uk
 * @property string             $description_ru
 * @property string             $description_en
 * @property string             $main_img
 * @property integer            $parent_id
 * @property string             $updated_at
 * @property string             $created_at
 *
 * @property Category           $parent
 * @property Product[]          $products
 * @property PropertyCategory[] $propertyCategories
 */
class Category extends BaseModel
{
    public $parentId, $autoCache = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_ru'], 'required'],
            ['main_img', 'required', 'on' => 'create'],
            [['description_uk', 'description_ru', 'description_en'], 'string'],
            [['parentId'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title_uk', 'title_ru', 'title_en', 'main_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'title_uk'       => 'Название',
            'title_ru'       => 'Название',
            'title_en'       => 'Название',
            'description_uk' => 'Описание',
            'description_ru' => 'Описание',
            'description_en' => 'Описание',
            'main_img'       => 'Изображения',
            'parentId'       => 'Вложенность',
            'updated_at'     => 'Дата обновления',
            'created_at'     => 'Дата создания',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return ArrayHelper::merge(
            $behaviors,
            [
                'uploadBehavior' => [
                    'class'      => UploadBehavior::class,
                    'attributes' => ['main_img'],
                    'path'       => $this->mainImgPath(),
                    'tempPath'   => $this->mainImgTempPath(),
                ],
                'seo'            => [
                    'class'         => 'common\modules\seo\behaviors\SeoBehavior',
                    'model'         => $this->getModelName(),
                    'view_action'   => '/catalog/default/category-view',
                    'view_category' => 'catalog/category',
                ],
                'search'         => [
                    'class' => SearchBehavior::class,
                ],
            ]
        );
    }

    /**
     * @param null $image
     *
     * @return bool|string
     */
    public function mainImgPath($image = null)
    {
        $path = $this->modelUploadsPath();
        if ($image !== null) {
            $path .= '/' . $image;
        }
        return $path;
    }

    /**
     * @param null $image
     *
     * @return string
     * @throws \yii\base\Exception
     */
    public static function mainImgTempPath($image = null)
    {
        $path = self::moduleUploadsPath();
        if ($image !== null) {
            $path .= '/' . $image;
        }
        return $path;
    }

    public function getSearchScope()
    {
        return [
            'select' => [
                'category.title_uk',
                'category.title_ru',
                'category.title_en',
                'category.description_uk',
                'category.description_ru',
                'category.description_en',
                'category.id',
                'category.main_img',
            ],
            'with'   => 'seo',
        ];
    }

    public function getIndexFields()
    {
        return [
            ['name' => 'model_name', 'value' => 'category', 'type' => SearchBehavior::FIELD_UNINDEXED],
            ['name' => 'model_id', 'value' => $this->id, 'type' => SearchBehavior::FIELD_UNINDEXED],
            ['name' => 'title_uk', 'value' => $this->title_uk],
            ['name' => 'title_ru', 'value' => $this->title_ru],
            ['name' => 'title_en', 'value' => $this->title_en],
            ['name' => 'content_uk', 'value' => strip_tags($this->description_uk)],
            ['name' => 'content_ru', 'value' => strip_tags($this->description_ru)],
            ['name' => 'content_en', 'value' => strip_tags($this->description_en)],
            ['name' => 'url', 'value' => $this->getSeoUrl(), 'type' => SearchBehavior::FIELD_KEYWORD],
            ['name' => 'img', 'value' => $this->getMainImg(200, 200, ['class' => 'media-object'])],
        ];
    }

    public function getMainImg($width = 300, $height = 400, $htmlOptions = [])
    {
        $defaultOptions = [
            'alt'   => $this->getMlTitle(),
            'class' => 'img-responsive',
        ];
        $options = ArrayHelper::merge($defaultOptions, $htmlOptions);
        if ($this->main_img != null) {
            if ($width != 300 && $height != 400) {
                $path = makeDynamicImageThumbUrl($this->main_img, $width, $height);
                //                $path = Image::getThumb($width, $height, $this->getModelName(), $this->id, $this->main_img, [
                //                    'native_dir' => true,
                //                ]);
            } else {
                $path = $this->modelUploadsUrl() . $this->main_img;
            }
        } else {
            $path = 'http://placehold.it/' . $width . 'x' . $height;
        }

        return Html::img($path, $options);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    public function getPropertyCategories()
    {
        return $this->hasMany(PropertyCategory::class, ['id' => 'property_category_id'])
                    ->viaTable('property_category_catalog_category', ['category_id' => 'id']);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function buildSeoData()
    {
        return [
            'title_uk'       => $this->title_uk,
            'title_ru'       => $this->title_ru,
            'title_en'       => $this->title_en,
            'description_uk' => getShortText($this->description_uk, 200, true),
            'description_ru' => getShortText($this->description_ru, 200, true),
            'description_en' => getShortText($this->description_en, 200, true),
            'keywords_uk'    => $this->title_uk,
            'keywords_ru'    => $this->title_ru,
            'keywords_en'    => $this->title_en,
        ];
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        $parent = $this->parent()->one();
        return $parent['id'];
    }

    public function getChildrenList($as_array = false, $map = false)
    {
        $lang = Yii::$app->language;
        $title = 'title_' . $lang;
        $cacheKey = $lang . 'ChildrenListForCategory' . $this->id;

        if ($as_array) {
            $cacheKey .= 'AsArray';
        }

        $models = Yii::$app->cache->get($cacheKey);

        if ($models === false) {
            $models = $this->children()->with('seo')->orderBy($title . ' ASC')->asArray($as_array)->all();

            Yii::$app->cache->set($cacheKey, $models, self::DEFAULT_CACHE_DURATION);
        }

        if (!$map) {
            return $models;
        }
        return ArrayHelper::map($models, 'id', $title);
    }

    public function beforeDelete()
    {
        PropertyCategoryCatalogCategory::deleteAll(['category_id' => $this->id]);
        return parent::beforeDelete();
    }

    /**
     * @param bool $updateCache
     *
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['parent_id' => 'id']);
    }


    public function getMlContent($lang = null, $attribute = 'description')
    {
        return parent::getMlContent($lang, $attribute);
    }
}
