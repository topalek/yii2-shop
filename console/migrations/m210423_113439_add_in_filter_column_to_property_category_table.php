<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%property_category}}`.
 */
class m210423_113439_add_in_filter_column_to_property_category_table extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%property_category}}',
            'in_filters',
            $this->boolean()->defaultValue(0)->after('title_en')->comment('Использовать в фильтрах')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%property_category}}', 'in_filters');
    }
}
