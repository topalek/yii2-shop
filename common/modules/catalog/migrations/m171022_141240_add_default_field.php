<?php

use yii\db\Migration;

class m171022_141240_add_default_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('catalog_item_property', 'default', $this->boolean()->after('photo')->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('catalog_item_property', 'default');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171022_141240_add_default_field cannot be reverted.\n";

        return false;
    }
    */
}
