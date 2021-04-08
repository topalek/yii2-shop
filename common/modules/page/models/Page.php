<?php

namespace common\modules\page\models;

use common\components\BaseModel;
use common\modules\search\behaviors\SearchBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "page".
 *
 * @property integer $id
 * @property string  $title_uk
 * @property string  $title_ru
 * @property string  $title_en
 * @property string  $content_uk
 * @property string  $content_ru
 * @property string  $content_en
 * @property integer $status
 * @property string  $updated_at
 * @property string  $created_at
 */
class Page extends BaseModel
{
    public $autoCache = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_ru', 'content_ru'], 'required'],
            [['title_uk', 'title_ru', 'title_en'], 'string', 'max' => 255],
            [['content_uk', 'content_ru', 'content_en'], 'string'],
            [['status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return ArrayHelper::merge(
            $behaviors,
            [
                'seo'    => [
                    'class'         => 'common\modules\seo\behaviors\SeoBehavior',
                    'model'         => $this->getModelName(),
                    'view_category' => '',
                    'view_action'   => 'page/default/view',
                ],
                'search' => [
                    'class' => SearchBehavior::class,
                ],
            ]
        );
    }

    public function getSearchScope()
    {
        return [
            'select'   => [
                'page.title_uk',
                'page.title_ru',
                'page.title_en',
                'page.content_uk',
                'page.content_ru',
                'page.content_en',
                'page.id',
            ],
            'joinWith' => 'seo',
        ];
    }

    public function getIndexFields()
    {
        return [
            ['name' => 'model_name', 'value' => 'page', 'type' => SearchBehavior::FIELD_UNINDEXED],
            ['name' => 'model_id', 'value' => $this->id, 'type' => SearchBehavior::FIELD_UNINDEXED],
            ['name' => 'title_uk', 'value' => $this->title_uk],
            ['name' => 'title_ru', 'value' => $this->title_ru],
            ['name' => 'title_en', 'value' => $this->title_en],
            ['name' => 'content_uk', 'value' => getShortText($this->content_uk, 350, true)],
            ['name' => 'content_ru', 'value' => getShortText($this->content_ru, 350, true)],
            ['name' => 'content_en', 'value' => getShortText($this->content_en, 350, true)],
            ['name' => 'url', 'value' => $this->seoUrl, 'type' => SearchBehavior::FIELD_KEYWORD],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'title_uk'   => 'Название uk',
            'title_ru'   => 'Название',
            'title_en'   => 'Название en',
            'content_uk' => 'Контент uk',
            'content_ru' => 'Контент',
            'content_en' => 'Контент en',
            'status'     => 'Публиковать',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата создания',
        ];
    }

    public function getMlTitle($lang = null, $attribute = null)
    {
        $title = 'title_' . Yii::$app->language;
        if ($this->$title == null) {
            $title = 'title_ru';
        }
        return $this->$title;
    }

    public function getMlContent($lang = null, $attribute = 'content')
    {
        $content = 'content_' . Yii::$app->language;
        if ($this->$content == null) {
            $content = 'content_ru';
        }
        return $this->$content;
    }
}
