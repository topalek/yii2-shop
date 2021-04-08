<?php

use yii\db\Migration;

class m160323_181336_add_price_field extends Migration
{
    public function up()
    {
        $this->addColumn(
            'catalog_item_property',
            'price',
            $this->decimal(8, 2) . ' COMMENT "Ціна" AFTER property_category_id'
        );
    }

    public function down()
    {
        $this->dropColumn('catalog_item_property', 'price');
    }
}
