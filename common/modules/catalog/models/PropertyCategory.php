<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "property_category".
 *
 * @property int                               $id
 * @property string                            $title_uk
 * @property string                            $title_ru
 * @property string                            $title_en
 * @property bool                              $in_filters
 * @property string                            $updated_at
 * @property string                            $created_at
 *
 * @property Property[]                        $properties
 * @property-read string                       $catalogCategoryList
 * @property PropertyCategoryCatalogCategory[] $catalogCategories
 */
class PropertyCategory extends BaseModel
{
    public $catalogCategoryIds = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_ru'], 'required'],
            ['catalogCategoryIds', 'each', 'rule' => ['integer']],
            [['updated_at', 'created_at', 'in_filters'], 'safe'],
            [['in_filters'], 'boolean'],
            [['title_uk', 'title_ru', 'title_en'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'title_uk'           => 'Название (uk)',
            'title_ru'           => 'Название (ru)',
            'title_en'           => 'Название (en)',
            'in_filters'         => 'Использовать в фильтрах',
            'catalogCategoryIds' => 'Категория в каталоге',
            'updated_at'         => 'Дата обновления',
            'created_at'         => 'Дата создания',
        ];
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->syncCategories();
        self::getPropertiesList(false, true);
    }

    public function syncCategories()
    {
        $catalogCategories = $this->catalogCategories;
        if ($this->catalogCategoryIds != null) {
            if ($catalogCategories == null) // якщо ще не було категорій, зразу додаємо
            {
                foreach ($this->catalogCategoryIds as $newCatId) {
                    $newCategory = new PropertyCategoryCatalogCategory();
                    $newCategory->property_category_id = $this->id;
                    $newCategory->category_id = $newCatId;
                    $newCategory->save(false);
                }
            } else {
                $oldCategories = [];

                foreach ($catalogCategories as $old) {
                    $oldCategories[] = $old['category_id'];
                }

                $resultForAdd = array_diff($this->catalogCategoryIds, $oldCategories); // перевірка на нові

                if ($resultForAdd != null) {
                    foreach ($resultForAdd as $newCatId) {
                        $newCategory = new PropertyCategoryCatalogCategory();
                        $newCategory->property_category_id = $this->id;
                        $newCategory->category_id = $newCatId;
                        $newCategory->save(false);
                    }
                } else {
                    $resultForRemove = array_diff(
                        $oldCategories,
                        $this->catalogCategoryIds
                    ); // перевікра на видалення існуючих

                    if ($resultForRemove != null) {
                        foreach ($resultForRemove as $removeId) {
                            PropertyCategoryCatalogCategory::deleteAll(
                                ['category_id' => $removeId, 'property_category_id' => $this->id]
                            );
                        }
                    }
                }
            }
        } elseif ($catalogCategories != null) {
            PropertyCategoryCatalogCategory::deleteAll(['property_category_id' => $this->id]);
        }
    }

    public function getPropertiesList($map = true, $updateCache = false)
    {
        $cacheKey = 'propertiesListForCategory' . $this->id;
        $models = false;
        if (!$updateCache) {
            $models = Yii::$app->cache->get($cacheKey);
        }

        if ($models === false) {
            $models = Property::find()->where(['property_category_id' => $this->id])->all();

            Yii::$app->cache->set($cacheKey, $models, self::DEFAULT_CACHE_DURATION);
        }

        if ($updateCache) {
            return null;
        }

        if ($map) {
            return ArrayHelper::map($models, 'id', 'title_ru');
        }
        return $models;
    }

    public function beforeDelete()
    {
        PropertyCategoryCatalogCategory::deleteAll(['property_category_id' => $this->id]);
        Property::deleteAll(['property_category_id' => $this->id]);
        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::class, ['property_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogCategories()
    {
        return $this->hasMany(PropertyCategoryCatalogCategory::class, ['property_category_id' => 'id']);
    }

    public function getCatalogCategoryList()
    {
        $result = '';
        foreach ($this->catalogCategories as $key => $catalogCategory) {
            if ($key > 0) {
                $result .= ', ';
            }
            $result .= $catalogCategory->catalogCategory->title_ru;
        }
        return $result;
    }

    public static function find()
    {
        return new PropertyCategoryQuery(get_called_class());
    }
}
