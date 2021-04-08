<?php

use yii\db\Expression;
use yii\db\Migration;
use yii\db\Schema;

class m141110_111044_create_params_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'params',
            [
                'id'         => Schema::TYPE_PK,
                'name'       => 'string not null',
                'sys_name'   => 'string not null',
                'required'   => Schema::TYPE_SMALLINT . '(1) null DEFAULT "0"',
                'value'      => 'text not null',
                'status'     => 'tinyint not null default "1"',
                'updated_at' => 'timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                'created_at' => 'timestamp not null',
            ]
        );

        $this->insert(
            'params',
            [
                'name'       => 'Email адміна',
                'sys_name'   => 'adminEmail',
                'required'   => 1,
                'value'      => 'admin@example.com',
                'updated_at' => new Expression('NOW()'),
                'created_at' => new Expression('NOW()'),
            ]
        );
    }

    public function down()
    {
        $this->dropTable('params');
    }
}
