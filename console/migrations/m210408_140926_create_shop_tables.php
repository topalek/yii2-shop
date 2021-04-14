<?php

use yii\db\Migration;

/**
 * Class m210408_140926_create_shop_tables
 */
class m210408_140926_create_shop_tables extends Migration
{


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(
            '{{%cart}}',
            [
                'id'         => $this->primaryKey(),
                'sid'        => $this->string()->notNull(),
                'products'   => $this->json()->notNull(),
                'updated_at' => $this->timestamp()->notNull()
                    ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                    ->comment('Дата обновления'),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment(
                    'Дата создания'
                ),
            ]
        );

        $this->createTable(
            '{{%order}}',
            [
                'id'            => $this->primaryKey(),
                'name'          => $this->string()->notNull(),
                'email'         => $this->string()->notNull(),
                'phone'         => $this->string(),
                'delivery_info' => $this->text(),
                'products'      => $this->text()->notNull(),
                'status'        => $this->smallInteger()->defaultValue(0),
                'updated_at'    => $this->timestamp()->notNull()
                    ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                    ->comment('Дата обновления'),
                'created_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment(
                    'Дата создания'
                ),
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%cart}}');
        $this->dropTable('{{%order}}');
    }

}
