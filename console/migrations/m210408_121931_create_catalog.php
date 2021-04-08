<?php

use yii\db\Migration;

/**
 * Class m210408_121931_create_catalog
 */
class m210408_121931_create_catalog extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(
            'category',
            [
                'id'             => $this->primaryKey(),
                'title_uk'       => $this->string()->comment('Название'),
                'title_ru'       => $this->string()->notNull()->comment('Название'),
                'title_en'       => $this->string()->comment('Название'),
                'description_uk' => $this->text()->comment('Описание'),
                'description_ru' => $this->text()->comment('Описание'),
                'description_en' => $this->text()->comment('Описание'),
                'main_img'       => $this->string()->comment('Изображение'),
                'parent_id'      => $this->integer(),
                'updated_at'     => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ')->comment(
                    'Дата создания'
                ),
            ]
        );

        $this->createTable(
            'property_category',
            [
                'id'         => $this->primaryKey(),
                'title_uk'   => $this->string()->comment('Название'),
                'title_ru'   => $this->string()->notNull()->comment('Название'),
                'title_en'   => $this->string()->comment('Название'),
                'updated_at' => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ')->comment(
                    'Дата создания'
                ),
            ]
        );

        $this->createTable(
            'property_category_catalog_category',
            [
                'id'                   => $this->primaryKey(),
                'property_category_id' => $this->integer()->notNull(),
                'category_id'          => $this->integer()->notNull(),
                'updated_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP '
                )->comment('Дата создания'),
            ]
        );

        $this->createTable(
            'property',
            [
                'id'                   => $this->primaryKey(),
                'title_uk'             => $this->string()->comment('Название'),
                'title_ru'             => $this->string()->notNull()->comment('Название'),
                'title_en'             => $this->string()->comment('Название'),
                'property_category_id' => $this->integer()->notNull()->comment('Категория'),
                'updated_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP '
                )->comment('Дата создания'),
            ]
        );

        $this->createTable(
            'product',
            [
                'id'             => $this->primaryKey(),
                'title_uk'       => $this->string()->comment('Название'),
                'title_ru'       => $this->string()->notNull()->comment('Название'),
                'title_en'       => $this->string()->comment('Название'),
                'description_uk' => $this->text()->comment('Описание'),
                'description_ru' => $this->text()->comment('Описание'),
                'description_en' => $this->text()->comment('Описание'),
                'price'          => $this->decimal(8, 2)->comment('Цена'),
                'main_img'       => $this->string()->comment('Изображение'),
                'category_id'    => $this->integer()->notNull()->comment('Категория'),
                'status'         => $this->integer()->notNull()->defaultValue(1)->comment('Статус'),
                'updated_at'     => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at'     => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP '
                )->comment('Дата создания'),
            ]
        );

        $this->createTable(
            'product_property',
            [
                'id'                   => $this->primaryKey(),
                'product_id'           => $this->integer()->notNull(),
                'property_id'          => $this->integer()->notNull(),
                'property_category_id' => $this->integer()->notNull(),
                'price'                => $this->decimal(8, 2)->comment('Цена'),
                'default'              => $this->boolean()->defaultValue(0),
                'photo'                => $this->string(),
                'updated_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )->comment('Дата обновления'),
                'created_at'           => $this->timestamp()->notNull()->defaultExpression(
                    'CURRENT_TIMESTAMP '
                )->comment('Дата создания'),
            ]
        );


        $this->createIndex('category_updated_at', 'category', 'updated_at');
        $this->createIndex('property_category_updated_at', 'property_category', 'updated_at');
        $this->createIndex(
            'property_category_catalog_category_updated_at',
            'property_category_catalog_category',
            'updated_at'
        );
        $this->createIndex('property_updated_at', 'property', 'updated_at');
        $this->createIndex('product_updated_at', 'product', 'updated_at');
        $this->createIndex('product_property_updated_at', 'product_property', 'updated_at');

        $this->addForeignKey(
            'fk_product_category',
            'product',
            'category_id',
            'category',
            'id'
        );
        $this->addForeignKey(
            'fk_property_category_catalog_category',
            'property_category_catalog_category',
            'category_id',
            'category',
            'id'
        );
        $this->addForeignKey(
            'fk_property_category',
            'property',
            'property_category_id',
            'property_category',
            'id'
        );
        $this->addForeignKey(
            'fk_product_properties',
            'product_property',
            'product_id',
            'product',
            'id'
        );
        $this->addForeignKey(
            'fk_product_properties_property',
            'product_property',
            'property_id',
            'property',
            'id'
        );
    }

    public function down()
    {
        echo "m210408_121931_create_catalog cannot be reverted.\n";

        return false;
    }

}
