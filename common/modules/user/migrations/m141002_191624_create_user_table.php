<?php

use yii\db\Schema;
use yii\db\Migration;

class m141002_191624_create_user_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'user',
            [
                'id'           => 'pk',
                'username'     => 'string NOT NULL COMMENT "Логин"',
                'password'     => 'string NOT NULL COMMENT "Пароль"',
                'email'        => 'string(45) NULL COMMENT "E-mail"',
                'role'         => 'string NOT NULL DEFAULT "user" COMMENT "Роль"',
                'status'       => 'smallint NULL DEFAULT "0"',
                'auth_key'     => 'string NOT NULL',
                'access_token' => 'string NULL',
                'updated_at'   => 'timestamp NULL ON UPDATE CURRENT_TIMESTAMP COMMENT "Обновлено"',
                'created_at'   => 'timestamp NULL COMMENT "Добавлено"',
                'deleted_at'   => 'timestamp NULL COMMENT "Удалено"',
            ]
        );

        $this->createIndex('updated_at', 'user', 'updated_at');

        $admin_dump = file_get_contents(dirname(__FILE__) . '/../sql_data/admin.sql');
        $this->execute($admin_dump);
    }

    public function down()
    {
        $this->dropIndex('updated_at', 'user');
        $this->dropTable('user');
    }
}
