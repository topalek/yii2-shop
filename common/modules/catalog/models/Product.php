<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use common\modules\seo\behaviors\SeoBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "product".
 *
 * @property integer    $id
 * @property string     $title_uk
 * @property string     $title_ru
 * @property string     $title_en
 * @property string     $description_uk
 * @property string     $description_ru
 * @property string     $description_en
 * @property int        $price
 * @property string     $main_img
 * @property string[]   $additional_images
 * @property integer    $category_id
 * @property string     $article
 * @property integer    $status
 * @property integer    $stock
 * @property integer    $order_count
 * @property integer    $new
 * @property string     $updated_at
 * @property string     $created_at
 *
 * @property Category   $category
 * @property Property[] $properties
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
            'product' . $id,
            function () use ($id) {
                return self::find()
                    ->with(['properties', 'properties.property', 'properties.propertyCategory', 'category'])
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
            [['originalImgFile'], 'required', 'on' => 'create'],
            [['description_ru', 'description_uk', 'description_en', 'article'], 'string'],
            [['price'], 'number'],
            ['additional_images', 'each', 'rule' => ['string']],
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
            'id'                => 'ID',
            'title_uk'          => 'Название (uk)',
            'title_ru'          => 'Название (ru)',
            'title_en'          => 'Название (en)',
            'description_uk'    => 'Описание (uk)',
            'description_ru'    => 'Описание (ru)',
            'description_en'    => 'Описание (en)',
            'price'             => 'Цена',
            'stock'             => 'На складе',
            'order_count'       => 'Популярность',
            'article'           => 'Артикул',
            'new'               => 'Новинка',
            'main_img'          => 'Оригинальное изображения',
            'originalImgFile'   => 'Оригинальное изображения',
            'imgFiles'          => 'Дополнительные изображения',
            'additional_images' => 'Дополнительные изображения',
            'category_id'       => 'Категория',
            'status'            => 'Статус',
            'updated_at'        => 'Дата обновления',
            'created_at'        => 'Дата создания',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return ArrayHelper::merge(
            $behaviors,
            [
                'seo' => [
                    'class'         => SeoBehavior::class,
                    'model'         => $this->getModelName(),
                    'view_action'   => 'catalog/default/product-view',
                    'view_category' => 'catalog/product',
                ],
            ]
        );
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        Yii::$app->cache->delete('catalogItem' . $this->id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function initialPreviewConfig()
    {
        $config = [];
        foreach ($this->getAdditionalImgsUrl() as $i => $url) {
            $config[] = [
                'url'   => Url::toRoute(['product/delete-additional-img']),
                'key'   => $i,
                'extra' => [
                    'id' => $this->id,
                ],
            ];
        }
        return $config;
    }

    public function getAdditionalImgsUrl()
    {
        $imgList = [];
        if ($this->additional_images) {
            $imgList = array_map(
                function ($item) {
                    if (strpos($item, 'http') != false) {
                        return $item;
                    }
                    return isFrontendApp() ? $item : Yii::$app->params['frontendUrl'] . $item;
                },
                $this->additional_images
            );
        }
        return [...$imgList];
    }

    public function saveImg()
    {
        if ($this->originalImgFile) {
            @unlink($this->modelUploadsPath() . $this->main_img);
            $this->main_img = substr(md5(microtime()), 0, 10) . '.' . $this->originalImgFile->extension;
            $this->originalImgFile->saveAs($this->modelUploadsPath() . $this->main_img);
        }
        if ($this->imgFiles) {
            FileHelper::removeDirectory($this->modelUploadsPath() . 'additional');
            FileHelper::createDirectory($this->modelUploadsPath() . 'additional/');
            $imgArray = [];
            foreach ($this->imgFiles as $imgFile) {
                $imgFile->saveAs($this->modelUploadsPath() . 'additional/' . $imgFile->name);
                $imgArray[] = $this->modelUploadsUrl() . 'additional/' . $imgFile->name;
            }
            $this->additional_images = $imgArray;
        }
        $this->save(false);
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
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::class, ['id' => 'property_id'])->viaTable(
            ProductProperty::tableName(),
            ['product_id' => 'id']
        );
    }

    /**
     * @param null   $lang
     * @param string $attribute
     *
     * @return mixed
     */
    public function getMlContent($lang = null, $attribute = 'description')
    {
        return parent::getMlAttribute($lang, $attribute);
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

}
