<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;

/**
 * This is the model class for table "property_category_catalog_category".
 *
 * @property integer          $id
 * @property integer          $property_category_id
 * @property integer          $catalog_category_id
 * @property string           $updated_at
 * @property string           $created_at
 *
 * @property Category         $catalogCategory
 * @property PropertyCategory $propertyCategory
 */
class PropertyCategoryCatalogCategory extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_category_catalog_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_category_id', 'category_id'], 'required'],
            [['property_category_id', 'category_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'property_category_id' => 'Property Category ID',
            'category_id'          => 'Category ID',
            'updated_at'           => 'Updated At',
            'created_at'           => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategory()
    {
        return $this->hasOne(PropertyCategory::class, ['id' => 'property_category_id']);
    }
}
