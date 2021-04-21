<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;

/**
 * This is the model class for table "catalog_property".
 *
 * @property integer          $id
 * @property string           $title_uk
 * @property string           $title_ru
 * @property string           $title_en
 * @property integer          $property_category_id
 * @property string           $updated_at
 * @property string           $created_at
 *
 * @property PropertyCategory $category
 */
class Property extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property';
    }

    static function getListByCategory($categoryId)
    {
        return self::find()->select(['title_ru', 'id'])
                   ->where(['property_category_id' => $categoryId])
                   ->orderBy('title_ru ASC')
                   ->indexBy('id')
                   ->column();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_ru', 'property_category_id'], 'required'],
            [['property_category_id'], 'integer'],
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
            'id'                   => 'ID',
            'title_uk'             => 'Название (uk)',
            'title_ru'             => 'Название (ru)',
            'title_en'             => 'Название (en)',
            'property_category_id' => 'Категория',
            'updated_at'           => 'Дата обновления',
            'created_at'           => 'Дата создания',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(PropertyCategory::class, ['id' => 'property_category_id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['product_id' => 'id'])->viaTable(
            ProductProperty::tableName(),
            ['property_id' => 'id']
        );
    }

}
