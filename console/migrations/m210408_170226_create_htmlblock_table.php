<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%html_block}}`.
 */
class m210408_170226_create_htmlblock_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%html_block}}',
            [
                'id'            => $this->primaryKey(),
                'title'         => $this->string()->notNull()->comment('Название'),
                'position'      => $this->smallInteger()->notNull()->comment('Позиция'),
                'content'       => $this->text()->notNull()->comment('Контент'),
                'status'        => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('Опубликовано'),
                'ordering'      => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('Опубликовано'),
                'redactor_mode' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Опубликовано'),
                'updated_at'    => $this->timestamp()->notNull()
                                        ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                                        ->comment('Дата обновления'),
                'created_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment(
                    'Дата создания'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%html_block}}');
    }
}


