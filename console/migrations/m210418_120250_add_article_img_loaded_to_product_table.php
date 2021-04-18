<?php

use yii\db\Migration;

/**
 * Class m210418_120250_add_article_img_loaded_to_product_table
 */
class m210418_120250_add_article_img_loaded_to_product_table extends Migration
{

    public function up()
    {
        $this->addColumn(
            '{{%product}}',
            'article',
            $this->string(255)->null()->after('category_id')->comment('Артикул')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%product}}', 'article');
    }

}
