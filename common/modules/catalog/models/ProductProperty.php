<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;

/**
 * This is the model class for table "product_property".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $property_id
 * @property integer          $property_category_id
 * @property string           $photo
 * @property string           $price
 * @property boolean          $default
 * @property string           $updated_at
 * @property string           $created_at
 *
 * @property PropertyCategory $propertyCategory
 * @property Property         $property
 */
class ProductProperty extends BaseModel
{
    public function rules()
    {
        return [
            [['product_id', 'property_id', 'property_category_id'], 'required'],
            [['product_id', 'property_id', 'property_category_id'], 'integer'],
            [['price'], 'number'],
            ['default', 'boolean'],
            [['updated_at', 'created_at'], 'safe'],
            [['photo'], 'string', 'max' => 255],
        ];
    }

    public static function tableName()
    {
        return 'product_property';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategory()
    {
        return self::hasOne(PropertyCategory::class, ['id' => 'property_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return self::hasOne(Property::class, ['id' => 'property_id']);
    }

    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'product_id'           => 'Product ID',
            'property_id'          => 'Характеристика',
            'property_category_id' => 'Категория',
            'photo'                => 'Фото',
            'price'                => 'Цена',
            'default'              => 'По умолчанию',
            'updated_at'           => 'Дата обновления',
            'created_at'           => 'Дата создания',
        ];
    }
}
