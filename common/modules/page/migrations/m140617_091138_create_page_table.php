<?php

class m140617_091138_create_page_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable(
            'page',
            [
                'id'         => $this->primaryKey(),
                'title_uk'   => $this->string()->notNull() . ' COMMENT "Назва"',
                'title_ru'   => $this->string() . ' COMMENT "Назва ru"',
                'title_en'   => $this->string() . ' COMMENT "Назва en"',
                'content_uk' => $this->text()->notNull() . ' COMMENT "Контент"',
                'content_ru' => $this->text() . ' COMMENT "Контент ru"',
                'content_en' => $this->text() . ' COMMENT "Контент en"',
                'status'     => $this->smallInteger(1)->defaultValue(0)->notNull(),
                'updated_at' => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at' => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createIndex('page_updated_at', 'page', 'updated_at');
    }

    public function down()
    {
        $this->dropIndex('page_updated_at', 'page');
        $this->dropTable('page');
    }
}
