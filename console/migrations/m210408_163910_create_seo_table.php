<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%seo}}`.
 */
class m210408_163910_create_seo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(
            '{{%seo}}',
            [
                'id'                     => $this->primaryKey(),
                'title_ru'               => $this->string(),
                'title_uk'               => $this->string(),
                'title_en'               => $this->string(),
                'description_ru'         => $this->string(),
                'description_uk'         => $this->string(),
                'description_en'         => $this->string(),
                'keywords_ru'            => $this->string(),
                'keywords_uk'            => $this->string(),
                'keywords_en'            => $this->string(),
                'head_block'             => $this->text(),
                'external_link'          => $this->string()->notNull(),
                'internal_link'          => $this->string()->notNull(),
                'external_link_with_cat' => $this->string()->defaultValue(null),
                'noindex'                => $this->smallInteger()->notNull()->defaultValue(0),
                'nofollow'               => $this->smallInteger()->notNull()->defaultValue(0),
                'in_sitemap'             => $this->smallInteger()->notNull()->defaultValue(1),
                'is_canonical'           => $this->smallInteger()->notNull()->defaultValue(0),
                'model_name'             => $this->string()->defaultValue(null),
                'model_id'               => $this->integer()->defaultValue(null),
                'status'                 => $this->smallInteger(1)->defaultValue(1)->comment("Публиковать"),
                'updated_at'             => $this->timestamp()->notNull()
                                                 ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                                                 ->comment('Дата обновления'),
                'created_at'             => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP'
                )->comment(
                    'Дата создания'
                ),
            ]
        );
        $this->insert(
            '{{%seo}}',
            [
                'title_ru'       => 'Главная страница',
                'title_uk'       => 'Головна сторінка',
                'description_ru' => 'Описание главной страницы',
                'description_uk' => 'Опис для головної сторінки',
                'keywords_ru'    => 'Ключевые слова для головной страницы',
                'keywords_uk'    => 'Ключові слова для головної сторінки',
                'external_link'  => '/',
                'internal_link'  => 'site/index',
            ]
        );

        $this->createIndex('seo_external_link', '{{%seo}}', 'updated_at');
        $this->createIndex('seo_model_name', '{{%seo}}', 'model_name');
        $this->createIndex('seo_updated_at', '{{%seo}}', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex('seo_external_link', '{{%seo}}');
        $this->dropIndex('seo_model_name', '{{%seo}}');
        $this->dropIndex('seo_updated_at', '{{%seo}}');
        $this->dropTable('{{%seo}}');
    }
}
