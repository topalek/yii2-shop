<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "property_category".
 *
 * @property integer                           $id
 * @property string                            $title_uk
 * @property string                            $title_ru
 * @property string                            $title_en
 * @property string                            $updated_at
 * @property string                            $created_at
 *
 * @property Property[]                        $properties
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
            [['title_uk'], 'required'],
            ['catalogCategoryIds', 'each', 'rule' => ['integer']],
            [['updated_at', 'created_at'], 'safe'],
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
            'title_uk'           => 'Название',
            'title_ru'           => 'Название',
            'title_en'           => 'Название',
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
        Yii::$app->cache->delete('propertyListForCategory' . $this->id);
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
                    $newCategory->catalog_category_id = $newCatId;
                    $newCategory->save(false);
                }
            } else {
                $oldCategories = [];

                foreach ($catalogCategories as $old) {
                    $oldCategories[] = $old['catalog_category_id'];
                }

                $resultForAdd = array_diff($this->catalogCategoryIds, $oldCategories); // перевірка на нові

                if ($resultForAdd != null) {
                    foreach ($resultForAdd as $newCatId) {
                        $newCategory = new PropertyCategoryCatalogCategory();
                        $newCategory->property_category_id = $this->id;
                        $newCategory->catalog_category_id = $newCatId;
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
                                ['catalog_category_id' => $removeId, 'property_category_id' => $this->id]
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
            $result .= $catalogCategory->catalogCategory->title_uk;
        }
        return $result;
    }
}
