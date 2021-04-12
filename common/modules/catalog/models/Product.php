<?php

namespace common\modules\catalog\models;

use backend\extensions\fileapi\behaviors\UploadBehavior;
use common\components\BaseModel;
use common\modules\image\behaviors\ImagesBehavior;
use common\modules\search\behaviors\SearchBehavior;
use common\modules\seo\behaviors\SeoBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "product".
 *
 * @property integer         $id
 * @property string          $title_uk
 * @property string          $title_ru
 * @property string          $title_en
 * @property string          $description_uk
 * @property string          $description_ru
 * @property string          $description_en
 * @property int             $price
 * @property string          $main_img
 * @property integer         $category_id
 * @property integer         $status
 * @property integer         $stock
 * @property integer         $order_count
 * @property integer         $new
 * @property string          $updated_at
 * @property string          $created_at
 *
 * @property Category        $category
 * @property ProductProperty $properties
 */
class Product extends BaseModel
{
    const VIEW_TYPE_LIST = 'list';
    const VIEW_TYPE_BLOCK = 'block';
    public $propertyIds = [], $imgFiles, $originalImgFile;
    public $autoCache = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public static function findById($id)
    {
        return Yii::$app->cache->getOrSet(
            'catalogItem' . $id,
            function () use ($id) {
                return self::find()
                    ->with(['properties', 'properties.property', 'properties.propertyCategory'])
                    ->where(['product.id' => $id])
                    ->one();
            }
        );
    }

    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_ru', 'category_id'], 'required'],
            [['main_img'], 'required', 'on' => 'create'],
            [['description_ru', 'description_uk', 'description_en'], 'string'],
            [['price'], 'number'],
            [['category_id', 'status', 'stock', 'order_count', 'new'], 'integer'],
            [['updated_at', 'created_at', 'propertyIds'], 'safe'],
            [['title_uk', 'title_ru', 'title_en', 'main_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'title_uk'        => 'Название (uk)',
            'title_ru'        => 'Название (ru)',
            'title_en'        => 'Название (en)',
            'description_uk'  => 'Описание (uk)',
            'description_ru'  => 'Описание (ru)',
            'description_en'  => 'Описание (en)',
            'price'           => 'Цена',
            'stock'           => 'На складе',
            'order_count'     => 'Популярность',
            'new'             => 'Новинка',
            'main_img'        => 'Превью изображения',
            'originalImgFile' => 'Оригинальное изображения(600x800)',
            'imgFiles'        => 'Дополнительные изображения',
            'category_id'     => 'Категория',
            'status'          => 'Статус',
            'updated_at'      => 'Дата обновления',
            'created_at'      => 'Дата создания',
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
                'imagesBehavior' => [
                    'class'     => ImagesBehavior::class,
                    'attribute' => 'imgFiles',
                    'model'     => self::getModelName(),
                ],
                'seo'            => [
                    'class'         => SeoBehavior::class,
                    'model'         => $this->getModelName(),
                    'view_action'   => '/catalog/default/product-view',
                    'view_category' => 'catalog/product',
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

    public function beforeSave($insert)
    {
        if ($this->originalImgFile) {
            @unlink($this->modelUploadsPath() . $this->main_img);
            $this->original_img = substr(md5(microtime()), 0, 10) . '.jpg';
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->originalImgFile) {
            $this->originalImgFile->saveAs($this->modelUploadsPath() . $this->original_img);
        }
        Yii::$app->cache->delete('catalogItem' . $this->id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function getSearchScope()
    {
        return [
            'select' => [
                'product.title_uk',
                'product.title_ru',
                'product.title_en',
                'product.description_uk',
                'product.description_ru',
                'product.description_en',
                'product.id',
                'product.main_img',
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
            ['name' => 'img', 'value' => $this->getMainImg(['class' => 'media-object'])],
        ];
    }

    public function getMainImg($options = [])
    {
        $defaultOptions = [
            'alt'   => $this->getMlTitle(),
            'class' => 'img-responsive main-image',
        ];
        $options = ArrayHelper::merge($options, $defaultOptions);
        $path = $this->modelUploadsUrl() . $this->main_img;
        return Html::img($path, $options);
    }

    public function originalImgPreview()
    {
        return Html::img(
            $this->modelUploadsUrl() . $this->original_img,
            ['class' => 'img-responsive', 'style' => 'max-height:170px']
        );
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
            'keywords_uk'    => $this->title_uk . ', ' . $this->category->title_uk . ', ' . Yii::t(
                    'site',
                    'Магазин сувениров, копилки, статуэтки',
                    null,
                    'uk'
                ),
            'keywords_ru'    => $this->title_ru . ', ' . $this->category->title_ru . ', ' . Yii::t(
                    'site',
                    'Магазин сувениров, копилки, статуэтки',
                    null,
                    'ru'
                ),
            'keywords_en'    => $this->title_en . ', ' . $this->category->title_en . ', ' . Yii::t(
                    'site',
                    'Магазин сувениров, копилки, статуэтки',
                    null,
                    'en'
                ),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'catalog_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ProductProperty::class, ['product_id' => 'id']);
    }

    /**
     * @param null   $lang
     * @param string $attribute
     *
     * @return mixed
     */
    public function getMlContent($lang = null, $attribute = 'description')
    {
        return parent::getMlContent($lang, $attribute);
    }

    /**
     * @return array
     */
    public function buildNestedBreadcrumbs()
    {
        $result = [];
        foreach ($this->category->parents()->all() as $root) {
            $result[] = ['label' => $root->getMlTitle(), 'url' => $root->getSeoUrl()];
        }
        $result[] = ['label' => $this->category->getMlTitle(), 'url' => $this->category->getSeoUrl()];
        return $result;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        ProductProperty::deleteAll(['product_id' => $this->id]);
        Yii::$app->cache->delete('catalogItem' . $this->id);
        return parent::beforeDelete();
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        $price = asMoney($this->price_from);
        if ($this->price_to) {
            $price .= ' - ' . asMoney($this->price_to);
        }
        return $price;
    }
}
