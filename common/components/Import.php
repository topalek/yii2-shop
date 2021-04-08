<?php
/**
 * Created by topalek
 * Date: 06.04.2021
 * Time: 11:57
 */

namespace common\components;


use app\modules\catalog\models\Category;
use common\components\import\Msg;
use common\modules\shop\models\Partner;
use common\modules\shop\models\PartnerCategory;
use DOMDocument;
use XMLReader;
use Yii;
use yii\console\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\log\Logger;

class Import
{
    public $defaultFields = [
        'id'            => 'id',
        'code'          => 'article',
        'barcode'       => 'article',
        'vendorCode'    => 'article',
        'sku'           => 'article',
        'name'          => 'title',
        'description'   => 'description',
        'url'           => 'url',
        'image'         => 'image',
        'price'         => 'price',
        'oldprice'      => 'oldPrice',
        'purchasePrice' => 'purchasePrice',
        'available'     => 'available',
        'param'         => 'param',
        'vendor'        => 'brand',
        'brand'         => 'brand',
        'categoryId'    => 'categoryId',
    ];
    private $importFile;
    private $logger;
    private $fieldsNameRelation = [];
    private $importErrorText;

    public function __construct($filePath)
    {
        $this->importFile = $filePath;
        $this->logger = new Logger();
    }

    public function run()
    {
        if ($this->importCategories() && $this->importProducts()) {
            if ($this->importChars()) {
                if ($this->importCharValues()) {
                    $this->refreshShopItemChar();
                }
            }
            $this->updateShopItemsCount();
            if (($this->partnerImportConfig['setShopItemsCategories'] ?? false)) {
                $this->setShopItemsCategories();
            }
//                $this->writeSeoShopItems();
//                $this->compareCategories();
            $result = true;
        }
    }

    public function importCategories()
    {
        $this->_xmlCategories = $this->getXmlCategoryArray();
        if (!empty($this->_xmlCategories)) {
            $oldPartnerCategoryIds = (new Query())
                ->select(['xml_id'])
                ->from(Category::tableName())
                ->where(['partner_id' => $this->partnerId])
                ->column();

            if (!empty($oldPartnerCategoryIds)) {
                $newCategoryIds = array_keys($this->_xmlCategories);

                $categoryIdsToAdd = array_diff($newCategoryIds, $oldPartnerCategoryIds);

                if (!empty($categoryIdsToAdd)) {
                    $count = 0;
                    $sql = "SET NAMES utf8 COLLATE utf8_general_ci;";
                    $sql .= "\nINSERT INTO partner_category (title,partner_id,xml_id,xml_parent_id,sync_status) VALUES ";
                    $i = 0;
                    $counter = 0;
                    foreach ($categoryIdsToAdd as $key => $id) {
                        $title = Yii::$app->db->quoteValue($this->_xmlCategories[$id]['name']);
                        $parentId = ArrayHelper::getValue($this->_xmlCategories[$id], 'parentId', 0);
                        $values = "($title,$partnerId,$id,$parentId,0)";
                        if ($i == 1500) {
                            $sql .= "; \n INSERT INTO partner_category (title,partner_id,xml_id,xml_parent_id,0) VALUES $values, ";
                            $i = 0;
                        } else {
                            if ($i > 0) {
                                $sql .= ",";
                            }
                            $sql .= $values;
                            $i++;
                        }
                        usleep(1000);
                    }
                    file_put_contents($this->importDir . 'category.sql', $sql);
                    $this->runSql($this->importDir . 'category.sql');
                }

                $categoryIdsToRemove = array_diff($oldPartnerCategoryIds, $newCategoryIds);

                if (!empty($categoryIdsToRemove)) {
                    $count = count($categoryIdsToRemove);
                    Yii::$app->db->createCommand()
                        ->update(
                            Category::tableName(),
                            ['status' => 0, 'sync_status' => 0],
                            ['partner_id' => $this->partnerId, 'id' => $categoryIdsToRemove, 'status' => 1]
                        )
                        ->execute();
                }

                if (empty($categoryIdsToAdd) && (!isset($categoryIdsToRemove) || empty($categoryIdsToRemove))) {
                    $this->printR("\n Categories is equal \n");
                }
            } else {
                $this->printR(" \n No old partner categories, insert all \n");
                $sql = "SET NAMES utf8 COLLATE utf8_general_ci;";
                $sql .= "\nINSERT INTO partner_category (title,partner_id,xml_id,xml_parent_id,sync_status) VALUES ";
                $i = 0;
                $counter = 0;
                $count = 0;
                if ($this->interactiveMode) {
                    $count = count($this->_xmlCategories);
                    Console::startProgress(0, $count, 'Preparing partner_category_sql file ', false);
                }
                foreach ($this->_xmlCategories as $categoryId => $category) {
                    $title = Yii::$app->db->quoteValue($category['name']);
                    $parentId = ArrayHelper::getValue($category, 'parentId', 0);
                    $values = "($title,$partnerId,$categoryId,$parentId,0)";
                    if ($i == 1500) {
                        $sql .= "; \n INSERT INTO partner_category (title,partner_id,xml_id,xml_parent_id,sync_status) VALUES $values, ";
                        $i = 0;
                    } else {
                        if ($i > 0) {
                            $sql .= ",";
                        }
                        $sql .= $values;
                        $i++;
                    }
                    if ($this->interactiveMode) {
                        $counter++;
                        Console::updateProgress($counter, $count);
                    }
                }
                if ($this->interactiveMode) {
                    Console::endProgress("done." . PHP_EOL);
                }
                file_put_contents($this->importDir . 'category.sql', $sql);
                Yii::$app->db->createCommand($sql)->execute();
            }
            return true;
        }
        if ($this->importErrorText == null) {
            $this->importErrorText = 'Відсутні категорії';
        }
        return false;
    }

    public function getXmlCategoryArray()
    {
        $data = [];
        $categories = $this->parseXml('category');
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $nameKey = 'title';
                $data[$category['id']] = [
                    'name'     => ArrayHelper::getValue($category, $nameKey),
                    'parentId' => ArrayHelper::getValue($category, 'parentId', 0),
                ];
            }
        } else {
            if ($this->importErrorText == null) {
                $this->importErrorText = 'Категорії відсутні';
            }
            return false;
        }

        return $data;
    }

    /**
     * Parsing xml file according to $key_node
     *
     * @param      $key_node
     * @param null $filePath
     *
     * @return array|bool
     */
    private function parseXml($key_node, $filePath = null)
    {
        try {
            $output = [];
            $DOMDocument = new DOMDocument();
            $reader = new XMLReader();
            $reader->open($filePath);
            $line = 0;
            while (($read = $reader->read()) && $reader->name !== $key_node) {
                $line++;
            }
            if (!$read) {
                $text = "Не можливо прочитати xml файл - {$filePath}<br>";
                $text .= "Елемент - {$key_node} відсутній у файлі<br>";
                $this->importErrorText = $text;
                return $output = [];
            }
            $i = 0;
            while ($reader->name === $key_node) {
                try {
                    $expanded = $reader->expand();
                } catch (Exception $e) {
                    $this->logger->log($e->getMessage(), 1);
                    try {
                        $reader->next($key_node);
                    } catch (Exception $e) {
                        $this->logger->log($e->getMessage(), 1);
                    }
                }
                if (isset($expanded) && $expanded) {
                    if ($expanded->attributes) {
                        $dom = simplexml_import_dom($DOMDocument->importNode($expanded, true));
                        foreach ($dom->attributes() as $k => $v) {
                            $fieldName = mb_convert_encoding(trim($k), 'utf-8');
                            if (array_key_exists($fieldName, $this->fieldsNameRelation)) {
                                $fieldName = $this->fieldsNameRelation[$fieldName];
                            }
                            $output[$i][$fieldName] = mb_convert_encoding(
                                trim((string)$v),
                                'utf-8'
                            );
                            $output[$i]['title'] = mb_convert_encoding(trim((string)$expanded->nodeValue), 'utf-8');
                        };
                    }

                    foreach ($expanded->childNodes as $k => $item) {
                        if ($item->nodeType == 1) {
                            if ($item->attributes->length > 0) {
                                foreach ($item->attributes as $prop) {
                                    if ((string)$prop->nodeValue) {
                                        $fieldName = trim((string)$item->nodeName);
                                        if (array_key_exists($fieldName, $this->fieldsNameRelation)) {
                                            $fieldName = $this->fieldsNameRelation[$fieldName];
                                        }
                                        $output[$i][$fieldName][trim((string)$prop->nodeValue)] = mb_convert_encoding(
                                            trim((string)$item->nodeValue),
                                            'utf-8'
                                        );
                                    }
                                }
                            } else {
                                $nodeName = trim((string)$item->nodeName);
                                if (array_key_exists($nodeName, $this->fieldsNameRelation)) {
                                    $nodeName = $this->fieldsNameRelation[$nodeName];
                                }
                                if (in_array($nodeName, ['picture', 'image'])) {
                                    $output[$i]['image'][] = mb_convert_encoding(
                                        trim((string)$item->nodeValue),
                                        'utf-8'
                                    );
                                } else {
                                    $output[$i][$nodeName] = mb_convert_encoding(
                                        trim((string)$item->nodeValue),
                                        'utf-8'
                                    );
                                }
                            }
                        }
                    }

                    if (!isset($output[$i]['code']) && !isset($output[$i]['vendorCode']) && !isset($output[$i]['barcode'])) {
                        $article = ArrayHelper::getValue($output[$i], 'param.Артикул');
                        if ($article) {
                            $output[$i]['article'] = $article;
                        }
                    }
                }
                try {
                    $reader->next($key_node);
                } catch (Exception $e) {
                    $this->readImportFileErrors[] = $e->getMessage();
                }
                $i++;
            }
            return $output;
        } catch (Exception $exception) {
            $this->importErrorText = 'Помилка зчитування файлу';
            return [];
        }
    }

    private function importProducts()
    {
    }

    private function importChars()
    {
    }

    private function importCharValues()
    {
    }
}
