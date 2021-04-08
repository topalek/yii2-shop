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
                'id' => $this->primaryKey(),
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
