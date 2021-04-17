<?php

namespace common\modules\catalog\models;

use common\components\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

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

    static function getListByCategory2($categoryId, $map = true)
    {
        $cacheKay = 'propertyListForCategory' . $categoryId;

        $models = Yii::$app->cache->get($cacheKay);

        if ($models === false) {
            $models = self::getDb()->cache(
                function () use ($categoryId) {
                    return self::find()->where(['property_category_id' => $categoryId])->orderBy(
                        'title_ru ASC'
                    )->asArray()->all();
                },
                self::DEFAULT_CACHE_DURATION,
                self::getDbDependency()
            );

            Yii::$app->cache->set($cacheKay, $models, self::DEFAULT_CACHE_DURATION);
        }

        if ($map) {
            return ArrayHelper::map($models, 'id', 'title_ru');
        }
        return $models;
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

    public function beforeSave($insert)
    {
        ProductProperty::deleteAll(['property_id' => $this->id]);
        return parent::beforeSave($insert);
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('propertiesListForCategory' . $this->property_category_id);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete('propertiesListForCategory' . $this->property_category_id);
    }


}
