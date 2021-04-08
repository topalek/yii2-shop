<?php

use yii\db\Migration;
use yii\db\Schema;

class m141008_084241_create_image_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'image',
            [
                'id'         => Schema::TYPE_PK,
                'model_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "Model name"',
                'model_id'   => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Model id"',
                'image'      => Schema::TYPE_STRING . ' NOT NULL COMMENT "Img name"',
                'is_main'    => Schema::TYPE_SMALLINT . '(1) NULL DEFAULT "0" COMMENT "Main image"',
                'ordering'   => Schema::TYPE_INTEGER . ' NULL DEFAULT "1" COMMENT "Ordering"',
                'updated_at' => 'timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                'created_at' => 'timestamp not null',
            ]
        );

        $this->createIndex('image_updated_at', 'image', 'updated_at');
        $this->createIndex('image_model_name', 'image', 'model_name');
    }

    public function down()
    {
        $this->dropIndex('image_updated_at', 'image');
        $this->dropIndex('image_model_name', 'image');
        $this->dropTable('image');
    }
}
