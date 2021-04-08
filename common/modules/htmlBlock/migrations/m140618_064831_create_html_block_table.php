<?php

use yii\db\Schema;

class m140618_064831_create_html_block_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable(
            'html_block',
            [
                'id'            => Schema::TYPE_PK,
                'title'         => 'string not null',
                'position'      => 'string not null',
                'content'       => 'text not null',
                'status'        => 'tinyint not null default "0"',
                'ordering'      => 'tinyint not null default "0"',
                'redactor_mode' => 'smallint(1) not null default "1"',
                'updated_at'    => 'timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                'created_at'    => 'timestamp not null',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('html_block');
    }
}
