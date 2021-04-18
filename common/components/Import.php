<?php
/**
 * Created by topalek
 * Date: 06.04.2021
 * Time: 11:57
 */

namespace common\components;


use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use DOMDocument;
use XMLReader;
use yii\console\Exception;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

class Import
{
    public $defaultFields = [
        'id'             => 'id',
        'code'           => 'article',
        'barcode'        => 'article',
        'vendorCode'     => 'article',
        'sku'            => 'article',
        'article'        => 'article',
        'name'           => 'title',
        'name_ua'        => 'title_uk',
        'description'    => 'description',
        'description_ua' => 'description_uk',
        'url'            => 'url',
        'image'          => 'image',
        'picture'        => 'image',
        'price'          => 'price',
        'oldprice'       => 'oldPrice',
        'purchasePrice'  => 'purchasePrice',
        'available'      => 'available',
        'param'          => 'param',
        'vendor'         => 'brand',
        'stock_quantity' => 'stock',
        'brand'          => 'brand',
        'categoryId'     => 'category_id',
    ];
    private $importFile;
    private $logger;
    private $fieldsNameRelation = [];
    private $importErrorText;
    private $_xmlCategories = [];
    private $_categories = [];
    private $_products = [];
    private $_xmlProducts = [];
    private array $readImportFileErrors;

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
        $categoriesToAdd = $this->getCategoriesToAdd();
        if (!$categoriesToAdd) {
            foreach ($categoriesToAdd as $newCategory) {
                $cat = new Category();
                $cat->title_ru = $newCategory['name'];
                $cat->parent_id = $newCategory['parentId'];
                $cat->save();
            }
            return true;
        }
        if ($this->importErrorText == null) {
            $this->importErrorText = 'Отсутствуют категории';
        }
        return false;
    }

    public function getCategoriesToAdd()
    {
        $categoriesToAdd = [];
        if ($this->getXmlCategoryArray()) {
            foreach ($this->getXmlCategoryArray() as $xmlCategory) {
                if (!in_array($xmlCategory['name'], $this->getCategories())) {
                    $categoriesToAdd[] = $xmlCategory;
                }
            }
        }
        return $categoriesToAdd;
    }

    public function getXmlCategoryArray()
    {
        if (!$this->_xmlCategories) {
            $categories = $this->parseXml('category');
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $nameKey = 'title';
                    $this->_xmlCategories[$category['id']] = [
                        'name'     => ArrayHelper::getValue($category, $nameKey),
                        'parentId' => ArrayHelper::getValue($category, 'parentId', 0),
                    ];
                }
            } else {
                if ($this->importErrorText == null) {
                    $this->importErrorText = 'Отсутствуют категории';
                }
                return false;
            }
        }

        return $this->_xmlCategories;
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
        $filePath = $filePath ?? $this->importFile;
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
                            if (array_key_exists($fieldName, $this->defaultFields)) {
                                $fieldName = $this->defaultFields[$fieldName];
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
                                        if (array_key_exists($fieldName, $this->defaultFields)) {
                                            $fieldName = $this->defaultFields[$fieldName];
                                        }
                                        $output[$i][$fieldName][trim((string)$prop->nodeValue)] = mb_convert_encoding(
                                            trim((string)$item->nodeValue),
                                            'utf-8'
                                        );
                                    }
                                }
                            } else {
                                $nodeName = trim((string)$item->nodeName);
                                if (array_key_exists($nodeName, $this->defaultFields)) {
                                    $nodeName = $this->defaultFields[$nodeName];
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
        if ($this->getXmlProductsArray()) {
            $this->fillProductCategory();
            $productsToAdd = $this->getProductsToAdd();
            if ($productsToAdd) {
                foreach ($productsToAdd as $addProduct) {
                    $newProduct = new Product();
                    $newProduct->title_ru = $addProduct['name'];
                    $newProduct->title_uk = $addProduct['name_ua'];
                    $newProduct->description_ru = $addProduct['description'];
                    $newProduct->description_uk = $addProduct['description_ua'];
                    $newProduct->article = $addProduct['article'];
                    $newProduct->status = $addProduct['available'] == 'true';
                    $newProduct->price = $addProduct['price'];
                    $newProduct->stock = $addProduct['stock'];
                    $newProduct->category_id = $addProduct['category_id'];
                    if (is_array($addProduct['image'])) {
                        $newProduct->main_img = array_shift($addProduct['image']);
                        $newProduct->additional_images = $addProduct['image'];
                    }
                    $newProduct->save();
                }
            }
            return true;
        }
        return false;
    }


    public function getXmlProductsArray()
    {
        if (!$this->_xmlProducts) {
            $products = $this->parseXml('offer');
            if (!empty($products)) {
                $this->_xmlProducts = $products;
            } else {
                if ($this->importErrorText == null) {
                    $this->importErrorText = 'Отсутствуют товары';
                }
                return false;
            }
        }

        return $this->_xmlProducts;
    }

    private function importChars()
    {
    }

    private function importCharValues()
    {
    }

    private function getProducts()
    {
        if (!$this->_products) {
            $this->_products = Product::find()->select(['article', 'id'])->indexBy('id')->column();
        }
        return $this->_products;
    }

    private function getCategories()
    {
        if (!$this->_categories) {
            $this->_categories = Category::find()->select(['title_ru', 'id'])->indexBy('id')->column();
        }
        return $this->_categories;
    }

    private function fillProductCategory()
    {
        foreach ($this->getXmlProductsArray() as $i => $xmlProduct) {
            if (array_key_exists($xmlProduct['categoryId'], $this->getXmlCategoryArray())) {
                $xmlCat = ArrayHelper::getValue($this->getXmlCategoryArray(), $xmlProduct['categoryId']);
                $xmlCategoryName = ArrayHelper::getValue($xmlCat, 'name');
                $this->_xmlProducts[$i]['categoryId'] = $xmlCategoryName;
                if (in_array($xmlCategoryName, $this->getCategories())) {
                    $categoryId = array_keys($this->getCategories(), $xmlCategoryName);
                    $categoryId = array_shift($categoryId);
                    $this->_xmlProducts[$i]['categoryId'] = $categoryId;
                }
            }
        }
    }

    private function getProductsToAdd()
    {
        $productsToAdd = [];
        foreach ($this->getXmlProductsArray() as $xmlProduct) {
            if (!in_array($xmlProduct['article'], $this->getProducts())) {
                $productsToAdd[] = $this->clearParams($xmlProduct);
            }
        }
        return $productsToAdd;
    }

    private function clearParams($xmlProduct)
    {
        $params = ArrayHelper::getValue($xmlProduct, 'param');
        if ($params) {
            foreach ($params as $i => $param) {
                if (is_int($i)) {
                    unset($params[$i]);
                }
            }
        }
        $xmlProduct['param'] = $params;
        return $xmlProduct;
    }
}
