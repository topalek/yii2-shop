<?php

namespace common\modules\translate\models;

/**
 * This is the model class for table "translate".
 *
 * @property integer         $id
 * @property string          $language
 * @property string          $translation
 *
 * @property SourceTranslate $source
 */
class Translate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'translate';
    }

    static function getLangList()
    {
        return [
            'ru' => 'Русский',
            //            'uk' => 'Українська',
            //            'en' => 'English',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'language'    => 'Язык',
            'translation' => 'Перевод',
            'message'     => 'Сообщение',
            'category'    => 'Категория',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(SourceTranslate::class, ['id' => 'id']);
    }
}
