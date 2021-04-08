<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user}}',
            [
                'id'                   => $this->primaryKey(),
                'username'             => $this->string()->notNull()->unique()->comment("Логин"),
                'auth_key'             => $this->string(32)->notNull(),
                'password_hash'        => $this->string()->notNull()->comment("Пароль"),
                'password_reset_token' => $this->string()->unique(),
                'email'                => $this->string()->notNull()->unique(),
                'role'                 => $this->string(45)->notNull()->defaultValue('user')->comment("Роль"),
                'status'               => $this->smallInteger()->notNull()->defaultValue(0)->comment("Статус"),
                'updated_at'           => $this->timestamp()->notNull()
                    ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                    ->comment('Дата обновления'),
                'created_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP'
                )->comment(
                    'Дата создания'
                ),
            ],
            $tableOptions
        );
        $this->createIndex('updated_at', '{{%user}}', 'updated_at');

        $this->insert(
            '{{%user}}',
            [
                'id'            => 1,
                'username'      => 'admin',
                'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
                'email'         => 'admin@gmail.com',
                'role'          => 'admin',
                'status'        => 1,
                'auth_key'      => 'NrmwHdntFKHOeTEXaWVCN0kZbeH4z97T',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
