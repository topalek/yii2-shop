<?php

use yii\db\Migration;

class m171022_133732_add_price_to_field extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('catalog_item', 'price', 'price_from');
        $this->addColumn('catalog_item', 'price_to', $this->decimal(8, 2)->after('price_from'));
    }

    public function safeDown()
    {
        $this->renameColumn('catalog_item', 'price_from', 'price_to');
        $this->dropColumn('catalog_item', 'price_to');
    }
}
