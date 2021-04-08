<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page}}`.
 */
class m210408_164606_create_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(
            '{{%page}}',
            [
                'id'         => $this->primaryKey(),
                'title_uk'   => $this->string()->comment('Название UA'),
                'title_ru'   => $this->string()->notNull()->comment('Название'),
                'title_en'   => $this->string()->comment('Название EN'),
                'content_uk' => $this->text()->comment('Контент UA'),
                'content_ru' => $this->text()->notNull()->comment('Контент'),
                'content_en' => $this->text()->comment('Контент EN'),
                'status'     => $this->tinyInteger(1)->notNull()->defaultValue(1),
                'updated_at' => $this->timestamp()->notNull()
                                     ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                                     ->comment('Дата обновления'),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment(
                    'Дата создания'
                ),
            ]
        );
        $this->createIndex('page_updated_at', '{{%page}}', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex('page_updated_at', '{{%page}}');
        $this->dropTable('{{%page}}');
    }
}
