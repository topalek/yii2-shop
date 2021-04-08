<?php

namespace common\modules\translate\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "source_translate".
 *
 * @property integer     $id
 * @property string      $category
 * @property string      $message
 *
 * @property Translate[] $messages
 */
class SourceTranslate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source_translate';
    }

    public static function getCategoryList()
    {
        return ArrayHelper::map(self::find()->groupBy('category')->all(), 'category', 'category');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'category' => 'Category',
            'message'  => 'Message',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Translate::class, ['id' => 'id']);
    }
}
