<?php

use yii\db\Migration;

class m160306_154204_create_catalog_tables extends Migration
{
    public function up()
    {
        $this->createTable(
            'catalog_category',
            [
                'id'             => $this->primaryKey(),
                'title_uk'       => $this->string()->notNull() . ' COMMENT "Назва"',
                'title_ru'       => $this->string() . ' COMMENT "Назва"',
                'title_en'       => $this->string() . ' COMMENT "Назва"',
                'description_uk' => $this->text() . ' COMMENT "Опис"',
                'description_ru' => $this->text() . ' COMMENT "Опис"',
                'description_en' => $this->text() . ' COMMENT "Опис"',
                'main_img'       => $this->string() . ' COMMENT "Зображення"',
                'tree'           => $this->integer(),
                'lft'            => $this->integer()->notNull(),
                'rgt'            => $this->integer()->notNull(),
                'depth'          => $this->integer()->notNull(),
                'updated_at'     => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at'     => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createTable(
            'property_category',
            [
                'id'         => $this->primaryKey(),
                'title_uk'   => $this->string()->notNull() . ' COMMENT "Назва"',
                'title_ru'   => $this->string() . ' COMMENT "Назва"',
                'title_en'   => $this->string() . ' COMMENT "Назва"',
                'updated_at' => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at' => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createTable(
            'property_category_catalog_category',
            [
                'id'                   => $this->primaryKey(),
                'property_category_id' => $this->integer()->notNull(),
                'catalog_category_id'  => $this->integer()->notNull(),
                'updated_at'           => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at'           => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createTable(
            'catalog_property',
            [
                'id'                   => $this->primaryKey(),
                'title_uk'             => $this->string()->notNull() . ' COMMENT "Назва"',
                'title_ru'             => $this->string() . ' COMMENT "Назва"',
                'title_en'             => $this->string() . ' COMMENT "Назва"',
                'property_category_id' => $this->integer()->notNull() . ' COMMENT "Категорія"',
                'updated_at'           => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at'           => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createTable(
            'catalog_item',
            [
                'id'                  => $this->primaryKey(),
                'title_uk'            => $this->string()->notNull() . ' COMMENT "Назва"',
                'title_ru'            => $this->string() . ' COMMENT "Назва"',
                'title_en'            => $this->string() . ' COMMENT "Назва"',
                'description_uk'      => $this->text() . ' COMMENT "Опис"',
                'description_ru'      => $this->text() . ' COMMENT "Опис"',
                'description_en'      => $this->text() . ' COMMENT "Опис"',
                'price'               => $this->decimal(8, 2) . ' COMMENT "Ціна"',
                'main_img'            => $this->string() . ' COMMENT "Головне зображення"',
                'original_img'        => $this->string() . ' COMMENT "Головне зображення"',
                'catalog_category_id' => $this->integer()->notNull() . ' COMMENT "Категорія"',
                'status'              => $this->integer()->notNull()->defaultValue('1') . ' COMMENT "Статус"',
                'updated_at'          => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at'          => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );

        $this->createTable(
            'catalog_item_property',
            [
                'id'                   => $this->primaryKey(),
                'catalog_item_id'      => $this->integer()->notNull(),
                'catalog_property_id'  => $this->integer()->notNull(),
                'property_category_id' => $this->integer()->notNull(),
                'photo'                => $this->string(),
                'updated_at'           => $this->timestamp()->notNull(
                    ) . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Дата обновления"',
                'created_at'           => $this->timestamp() . ' COMMENT "Дата создания"',
            ]
        );


        $this->createIndex('catalog_category_updated_at', 'catalog_category', 'updated_at');
        $this->createIndex('property_category_updated_at', 'property_category', 'updated_at');
        $this->createIndex(
            'property_category_catalog_category_updated_at',
            'property_category_catalog_category',
            'updated_at'
        );
        $this->createIndex('catalog_property_updated_at', 'catalog_property', 'updated_at');
        $this->createIndex('catalog_item_updated_at', 'catalog_item', 'updated_at');
        $this->createIndex('catalog_item_property_updated_at', 'catalog_item_property', 'updated_at');

        $this->addForeignKey(
            'fk_catalog_item_category',
            'catalog_item',
            'catalog_category_id',
            'catalog_category',
            'id'
        );
        $this->addForeignKey(
            'fk_property_category_catalog_category',
            'property_category_catalog_category',
            'catalog_category_id',
            'catalog_category',
            'id'
        );
        $this->addForeignKey(
            'fk_catalog_property_category',
            'catalog_property',
            'property_category_id',
            'property_category',
            'id'
        );
        $this->addForeignKey(
            'fk_catalog_item_properties',
            'catalog_item_property',
            'catalog_item_id',
            'catalog_item',
            'id'
        );
        $this->addForeignKey(
            'fk_catalog_item_properties_property',
            'catalog_item_property',
            'catalog_property_id',
            'catalog_property',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_catalog_item_properties_property', 'catalog_item_property');
        $this->dropForeignKey('fk_property_category_catalog_category', 'property_category_catalog_category');
        $this->dropForeignKey('fk_catalog_item_properties', 'catalog_item_property');
        $this->dropForeignKey('fk_catalog_item_category', 'catalog_property');
        $this->dropForeignKey('fk_catalog_property_category', 'catalog_item');

        $this->dropIndex('catalog_category_updated_at', 'catalog_category');
        $this->dropIndex('property_category_updated_at', 'property_category');
        $this->dropIndex('catalog_property_updated_at', 'catalog_property');
        $this->dropIndex('catalog_item_updated_at', 'catalog_item');
        $this->dropIndex('catalog_item_property_updated_at', 'catalog_item_property');
        $this->dropIndex('property_category_catalog_category_updated_at', 'property_category_catalog_category');

        $this->dropTable('catalog_item_property');
        $this->dropTable('catalog_item');
        $this->dropTable('catalog_property');
        $this->dropTable('property_category_catalog_category');
        $this->dropTable('property_category');
        $this->dropTable('catalog_category');
    }
}
