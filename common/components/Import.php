<?php
/**
 * Created by topalek
 * Date: 06.04.2021
 * Time: 11:57
 */

namespace common\components;


use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use common\modules\catalog\models\ProductProperty;
use common\modules\catalog\models\Property;
use common\modules\catalog\models\PropertyCategory;
use common\modules\seo\models\Seo;
use DOMDocument;
use XMLReader;
use Yii;
use yii\console\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
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
        'name_ua'        => 'title_ua',
        'description'    => 'description',
        'description_ua' => 'description_ua',
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
    public $importErrorText;
    private $_xmlCategories = [];
    private $_categories = [];
    private $_products = [];
    private $_xmlProducts = [];
    private $_params;
    private $formattedParamsArray;

    public function __construct($filePath)
    {
        $this->importFile = $filePath;
        $this->logger = new Logger();
    }

    public function run()
    {
        $this->deleteAllImages();
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();
        Seo::deleteAll(['>', 'id', 1]);
        Yii::$app->db->createCommand()->truncateTable(Category::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable(Product::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable(PropertyCategory::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable('{{%property_category_catalog_category}}')->execute();
        Yii::$app->db->createCommand()->truncateTable('{{%product_property}}')->execute();
        Yii::$app->db->createCommand()->truncateTable(Property::tableName())->execute();
        Yii::$app->db->createCommand()->checkIntegrity(true)->execute();
        if ($this->importCategories() && $this->importProducts()) {
            if ($this->importPropertyCategories()) {
                if ($this->importProperties()) {
                    $this->importErrorText = 'Все импортировано';
                }
            }
            $result = true;
        }
    }

    public function importCategories()
    {
        $categoriesToAdd = $this->getXmlCategoryArray();
        if (!empty($categoriesToAdd)) {
            foreach ($categoriesToAdd as $catId => $newCategory) {
                $cat = new Category();
                $cat->id = $catId;
                $cat->title_ru = $newCategory['name'];
                $cat->parent_id = $newCategory['parentId'] != 0 ? $newCategory['parentId'] : null;
                if (!$cat->save(false)) {
                    $this->importErrorText = $cat->firstErrors;
                }
            }
            return true;
        }
        $this->importErrorText = 'Отсутствуют категории';
        return false;
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
                $this->importErrorText[] = 'Отсутствуют категории';
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
            $this->importErrorText[] = 'Помилка зчитування файлу';
            return [];
        }
    }

    private function importProducts()
    {
        // if ($this->getXmlProductsArray()) {
        // $this->fillProductCategory();
        $productsToAdd = $this->getXmlProductsArray();
        if (!empty($productsToAdd)) {
            foreach ($productsToAdd as $addProduct) {
                $newProduct = new Product();
                $newProduct->id = $addProduct['id'];
                $newProduct->title_ru = $addProduct['title'];
                $newProduct->title_uk = $addProduct['title_ua'];
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
                if (!$newProduct->save(false)) {
                    $this->importErrorText = $newProduct->firstErrors;
                }
            }
            return true;
        }
        $this->importErrorText = 'Empty products';
        return false;
    }


    public function getXmlProductsArray()
    {
        if (!$this->_xmlProducts) {
            $products = $this->parseXml('offer');
            if (!empty($products)) {
                foreach ($products as $product) {
                    $this->_xmlProducts[] = $this->clearParams($product);
                }
            } else {
                $this->importErrorText[] = 'Отсутствуют товары';
                return false;
            }
        }

        return $this->_xmlProducts;
    }

    public function importPropertyCategories()
    {
        $newChars = $this->getXmlCharsArray();

        if (!empty($newChars)) {
            $newChars = array_unique($newChars);
            foreach ($newChars as $newChar) {
                $propCategory = new PropertyCategory();
                $propCategory->title_ru = $newChar;
                if (!$propCategory->save(false)) {
                    $this->importErrorText = $propCategory->firstErrors;
                }
                $propCategory->save();
            }
            return true;
        }
        $this->importErrorText = 'empty Property Categories';
        return false;
    }

    public function getXmlCharsArray()
    {
        $allParams = $this->getXmlParamsArray();

        $newChars = [];
        foreach ($allParams as $shopItemXmlId => $itemParams) {
            foreach ($itemParams as $paramTitle => $paramValue) {
                if (!in_array($paramTitle, $newChars)) {
                    $newChars[] = $paramTitle;
                }
            }
        }
        return $newChars;
    }

    public function getXmlParamsArray()
    {
        if ($this->_params == null) {
            $this->getParamsArray();
        }
        return $this->_params;
    }

    public function importProperties()
    {
        $formattedArrayParams = $this->getFormattedArrayParams();
        if (!empty($formattedArrayParams)) {
            foreach ($formattedArrayParams as $propCategoryId => $items) {
                foreach ($items as $propTitle => $productIds) {
                    $prop = new Property();
                    $prop->title_ru = $propTitle;
                    $prop->property_category_id = $propCategoryId;
                    if (!$prop->save()) {
                        $this->importErrorText = $prop->firstErrors;
                        return false;
                    }
                    foreach ($productIds as $productId) {
                        $prodProp = new ProductProperty();
                        $prodProp->property_category_id = $propCategoryId;
                        $prodProp->property_id = $prop->id;
                        $prodProp->product_id = $productId;
                        if (!$prodProp->save()) {
                            $this->importErrorText = $prodProp->firstErrors;
                            return false;
                        }
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    private function getCategories()
    {
        if (!$this->_categories) {
            $this->_categories = Category::find()->select(['title_ru', 'id'])->indexBy('id')->column();
        }
        return $this->_categories;
    }

    private function clearParams($xmlProduct)
    {
        $params = ArrayHelper::getValue($xmlProduct, 'param');
        unset($xmlProduct['param']);
        if ($params) {
            foreach ($params as $paramTitle => $param) {
                if (is_int($paramTitle)) {
                    unset($params[$paramTitle]);
                } elseif ($paramTitle == 'Детский возраст') {
                    unset($params[$paramTitle]);
                    $params['возраст'] = $param;
                } else {
                    unset($params[$paramTitle]);
                    $title = trim(mb_strtolower($paramTitle));
                    $params[$title] = $param;
                }
            }
        }
        $xmlProduct['params'] = $params;
        return $xmlProduct;
    }

    public function getFormattedArrayParams()
    {
        if ($this->formattedParamsArray != null) {
            return $this->formattedParamsArray;
        }
        $params = $this->getXmlParamsArray();
        $tmp = [];
        $existsPropCategories = $this->getExistsPropertyCategories();
        foreach ($params as $productId => $itemChars) {
            foreach ($itemChars as $charTitle => $charValue) {
                if (($charId = ArrayHelper::getValue($existsPropCategories, mb_strtolower($charTitle))) != null) {
                    $tmp[$charId][$charValue][] = $productId;
                }
            }
        }
        $this->formattedParamsArray = $tmp;
        unset($tmp);
        return $this->formattedParamsArray;
    }

    public function getExistsPropertyCategories()
    {
        if (empty($this->existsCharsArray)) {
            $this->existsCharsArray = PropertyCategory::find()->select(['id', 'title_ru'])->indexBy('title_ru')->column(
            );
        }
        return $this->existsCharsArray;
    }

    public function getParamsArray()
    {
        $shopItems = $this->getXmlProductsArray();
        foreach ($shopItems as $item) {
            $id = $item['id'];
            if (array_key_exists('params', $item)) {
                $params = array_filter($item['params']);
                $this->_params[$id] = $params;
            }
        }
    }

    private function deleteAllImages()
    {
        $dirArray = [
            Category::moduleUploadsPath(),
            Product::moduleUploadsPath(),
        ];
        foreach ($dirArray as $dir) {
            FileHelper::removeDirectory($dir);
        }
    }
}
