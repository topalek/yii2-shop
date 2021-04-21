<?php

namespace common\models;

/**
 * This is the model class for table "product_property".
 *
 * @property int      $product_id
 * @property int      $property_id
 * @property string   $updated_at Дата обновления
 * @property string   $created_at Дата создания
 *
 * @property Product  $product
 * @property Property $property
 */
class ProductProperty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'property_id'], 'required'],
            [['product_id', 'property_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['product_id', 'property_id'], 'unique', 'targetAttribute' => ['product_id', 'property_id']],
            [
                ['product_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Product::className(),
                'targetAttribute' => ['product_id' => 'id'],
            ],
            [
                ['property_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Property::className(),
                'targetAttribute' => ['property_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id'  => 'Product ID',
            'property_id' => 'Property ID',
            'updated_at'  => 'Дата обновления',
            'created_at'  => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Property]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'property_id']);
    }
}
