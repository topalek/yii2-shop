<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%params}}`.
 */
class m210408_140940_create_params_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%params}}',
            [
                'id'         => $this->primaryKey(),
                'name'       => $this->string()->notNull(),
                'sys_name'   => $this->string()->notNull(),
                'required'   => $this->smallInteger(1)->null()->defaultValue(0),
                'value'      => $this->text()->notNull(),
                'status'     => $this->tinyInteger(1)->notNull()->defaultValue(1),
                'updated_at' => $this->timestamp()->notNull()
                                     ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                                     ->comment('Дата обновления'),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment(
                    'Дата создания'
                ),
            ]
        );
        $this->insert(
            'params',
            [
                'name'     => 'Email адміна',
                'sys_name' => 'adminEmail',
                'required' => 1,
                'value'    => 'admin@example.com',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%params}}');
    }
}
