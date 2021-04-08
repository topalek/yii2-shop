<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%translate}}`.
 */
class m210408_165240_create_translate_table extends Migration
{
    /**
     * {@inheritdoc}
     *
     * CREATE TABLE source_translate (
     * id       INTEGER PRIMARY KEY AUTO_INCREMENT,
     * category VARCHAR(32),
     * message  TEXT
     * );
     * CREATE TABLE translate (
     * id          INTEGER,
     * language    VARCHAR(16),
     * translation TEXT,
     * PRIMARY KEY (id, language),
     * CONSTRAINT fk_translate_source_translate FOREIGN KEY (id)
     * REFERENCES source_translate (id)
     * ON DELETE CASCADE
     * ON UPDATE RESTRICT
     */
    public function up()
    {
        $this->createTable(
            '{{%source_translate}}',
            [
                'id'       => $this->primaryKey(),
                'category' => $this->string(32),
                'message'  => $this->text(),
            ]
        );
        $this->createTable(
            '{{%translate}}',
            [
                'id'          => $this->primaryKey(),
                'language'    => $this->string(16),
                'translation' => $this->text(),
            ]
        );
        $this->createIndex('idx_language', '{{%translate}}', 'language');
        $this->addForeignKey('fk_translate_source_translate', '{{%translate}}', 'id', 'source_translate', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_translate_source_translate', '{{%translate}}');
        $this->dropIndex('idx_language', '{{%translate}}');
        $this->dropTable('{{%translate}}');
        $this->dropTable('{{%source_translate}}');
    }
}
