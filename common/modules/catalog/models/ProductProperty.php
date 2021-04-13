<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
    /**
     * @inheritdoc
     */
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
        if ($this->default) {
            $sql = "SELECT id FROM product_property WHERE product_id = {$this->product_id}";
            if (!$insert) {
                $sql .= " AND id != {$this->id}";
            }
            $check = self::getDb()->createCommand($sql)->queryScalar();
            if ($check) {
                self::getDb()->createCommand()->update(self::tableName(), ['default' => 0], ['id' => $check])->execute();
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_property';
    }

    public function afterSave($insert, $changedAttributes)
    {
        Yii::$app->cache->delete('product' . $this->product_id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        Yii::$app->cache->delete('product' . $this->product_id);
        return parent::beforeDelete();
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

    public function getImg($width = 600, $height = 800, $htmlOptions = [])
    {
        $defaultOptions = [
            'class' => 'img-responsive',
        ];
        $options = ArrayHelper::merge($defaultOptions, $htmlOptions);

        if ($this->photo != null) {
            $path = makeDynamicImageThumbUrl($this->photo, $width, $height);
            //            $path = Image::getThumb($width, $height, $this->getModelName(), $this->id, $this->photo, [
            //                'native_dir' => true,
            //            ]);
        } else {
            $path = 'http://placehold.it/' . $width . 'x' . $height;
        }

        return Html::img($path, $options);
    }

    public function getImgPreview($width = 600, $height = 800)
    {
        if ($this->photo) {
            $thumbPath = makeDynamicImageThumbUrl($this->photo, $width, $height);

            //            $thumbPath = Image::getThumb($width, $height, $this->getModelName(), $this->id, $this->photo, [
            //                'native_dir' => true,
            //            ]);

            return [
                'thumb'    => $thumbPath,
                'fullSize' => $this->modelUploadsUrl() . $this->photo,
            ];
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
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
