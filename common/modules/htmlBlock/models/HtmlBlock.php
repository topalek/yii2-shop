<?php

namespace common\modules\htmlBlock\models;

use common\components\BaseModel;

/**
 * This is the model class for table "html_block".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $position
 * @property string  $content
 * @property integer $status
 * @property integer $ordering
 * @property integer $redactor_mode
 * @property string  $updated_at
 * @property string  $created_at
 */
class HtmlBlock extends BaseModel
{
    const REDACTOR_ON = 1;
    const REDACTOR_OFF = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'html_block';
    }

    static function getModeList()
    {
        return [
            self::REDACTOR_ON  => 'Да',
            self::REDACTOR_OFF => 'Нет',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'position', 'content'], 'required'],
            [['content'], 'string'],
            [['status', 'ordering', 'redactor_mode'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title', 'position'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'title'         => 'Название',
            'position'      => 'Позиция',
            'content'       => 'Содержание',
            'redactor_mode' => 'Включить редактор',
            'status'        => 'Публиковать',
            'ordering'      => 'Сортировка',
            'updated_at'    => 'Дата обновления',
            'created_at'    => 'Дата создания',
        ];
    }
}
