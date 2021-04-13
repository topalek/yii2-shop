<?php

use yii\db\Migration;

/**
 * Class m210413_091254_add_additional_images_to_product_table
 */
class m210413_091254_add_additional_images_to_product_table extends Migration
{


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn(
            '{{%product}}',
            'additional_images',
            $this->json()->after('main_img')->comment('Дополнительные изображения')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%product}}', 'additional_images');
    }

}
