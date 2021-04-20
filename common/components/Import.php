<?php
/**
 * Created by topalek
 * Date: 06.04.2021
 * Time: 11:57
 */

namespace common\components;


use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use common\modules\catalog\models\Property;
use common\modules\catalog\models\PropertyCategory;
use DOMDocument;
use XMLReader;
use Yii;
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
    private array $readImportFileErrors;
    private $_params;
    private $formattedParamsArray;

    public function __construct($filePath)
    {
        $this->importFile = $filePath;
        $this->logger = new Logger();
    }

    public function run()
    {
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();
        Yii::$app->db->createCommand()->truncateTable(Category::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable(Product::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable(PropertyCategory::tableName())->execute();
        Yii::$app->db->createCommand()->truncateTable('{{%property_category_catalog_category}}')->execute();
        Yii::$app->db->createCommand()->truncateTable('{{%product_property}}')->execute();
        Yii::$app->db->createCommand()->truncateTable(Property::tableName())->execute();
        Yii::$app->db->createCommand()->checkIntegrity(true)->execute();
        if ($this->importCategories() && $this->importProducts()) {
            if ($this->importChars()) {
                if ($this->importCharValues()) {
                    // $this->refreshShopItemChar();
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
                $cat->parent_id = $newCategory['parentId'];
                if (!$cat->save(false)) {
                    $this->importErrorText = $cat->firstErrors;
                }
            }
            return true;
        }
        $this->importErrorText = 'Отсутствуют категории';
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

    public function importChars()
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
            $this->importErrorText = 'empty Property Categories';
            return true;
        }
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

    public function setCharsRelation($params = '')
    {
        if ($params) {
            $this->_params = $params;
        }

        $partnerChars = CharPartnerAlias::getList(true, ['title', 'char.title'], ['partner_id' => $this->partnerId]);
        $charsValuesList = Char::getList(true, ['title', 'partner_char_values']);

        $params = [];

        foreach ($this->_params as $key => $values) {
            foreach ($values as $char => $value) {
                //TODO: Перевірити звідки у $value масив. Зараз не впливає ні на що. Все корректно працює
                if (is_array($value)) {
                    continue;
                }
                $newChar = ArrayHelper::getValue($partnerChars, $char, $char);
                $charsValues = ArrayHelper::getValue($charsValuesList, $char);

                if ($charsValues) {
                    $charsValues = Json::decode($charsValues);
                    if ($partner_char_values = ArrayHelper::getValue($charsValues, $this->partnerId)) {
                        foreach ($partner_char_values as $newValue => $items) {
                            if (is_array($items)) {
                                foreach ($items as $item) {
                                    if (mb_strtolower(trim($item)) == mb_strtolower($value)) {
                                        $value = $newValue;
                                        break;
                                    }
                                }
                            } else {
                                if (mb_strtolower(trim($items)) == mb_strtolower($value)) {
                                    $value = $newValue;
                                    break;
                                }
                            }
                        }
                    }
                }

                $params[$key][$newChar] = $value;
            }
        }

        $this->_params = $params;
    }

    public function importCharValues()
    {
        $formattedArrayParams = $this->getFormattedArrayParams();
        if (!empty($formattedArrayParams)) {
            if (empty($this->getExistsCharValues())) {
                foreach ($formattedArrayParams as $charCategoryId => $items) {
                    foreach ($items as $charTitle => $shopItemXmlIds) {
                    }
                }
                // $addedCharsCount = $this->makeAndRunCharValueSql($formattedArrayParams);

            }
            return true;
        } else {
            return false;
        }
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
                $productsToAdd[] = $xmlProduct;
            }
        }
        return $productsToAdd;
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
        $chars = $this->getXmlParamsArray();
        $tmp = [];
        $i = 0;
        $total = 0;

        $existsChars = $this->getExistsChars();
        foreach ($chars as $shopItemXmlId => $itemChars) {
            foreach ($itemChars as $charTitle => $charValue) {
                if (($charId = ArrayHelper::getValue($existsChars, mb_strtolower($charTitle))) != null) {
                    $tmp[$charId][$charValue][] = $shopItemXmlId;
                }
            }
        }

        $this->formattedParamsArray = $tmp;
        unset($tmp);
        return $this->formattedParamsArray;
    }

    public function getExistsChars()
    {
        if (empty($this->existsCharsArray)) {
            $this->existsCharsArray = PropertyCategory::find()->select(['title_ru', 'id'])->indexBy('id')->column();
        }

        return $this->existsCharsArray;
    }

    public function getExistsCharValues()
    {
        if (empty($this->existsCharValuesArray) || $refresh) {
            $this->existsCharValuesArray = (new Query())
                ->select(
                    [
                        'char_value.id',
                        'char_value.title',
                        'char_value_translate.value AS title_ru',
                        'char_value.char_id',
                    ]
                )
                ->from(CharValue::tableName())
                ->leftJoin('char_value_translate', 'char_value_translate.model_id = char_value.id')
                ->where(['char_value.char_id' => $this->charIds])
                ->all();
        }

        return $this->existsCharValuesArray;
    }

    public function makeAndRunCharValueSql($data)
    {
        $sql = "";
        $syncStatus = 0;
        $i = 0;
        $total = 0;

        if ($sql) {
            $this->printR(" printing char_value.sql\n");
            $sqlFile = $this->importDir . 'char_value.sql';
            file_put_contents($sqlFile, $sql . ";");
            $this->runSql($sqlFile);
        }

        return $total;
    }

    public function getShopItemCharDataArray()
    {
        $this->printR("Get shop item char data array \n");
        // $charsArray = [
        //  'char_id' => [
        //      char_value_title => char_value_id
        //  ]
        // ];
        $charsArray = [];
        foreach ($this->getExistsCharValues() as $items) {
            $char_id = $items['id'];
            $charsArray[$items['char_id']][$items['title']] = $char_id;
            if ($items['title_ru']) {
                $charsArray[$items['char_id']][$items['title_ru']] = $char_id;
            }
        }

        $this->printR("\n Getting shop_item ids \n");
        $shopItems = ShopItem::find()->select(['xml_id', 'id'])
                             ->where(
                                 [
                                     'original_partner_id' => $this->partnerId,
                                     'status'              => ShopItem::STATUS_PUBLISHED,
                                     'availability'        => ShopItem::AVAILABLE,
                                 ]
                             )
                             ->asArray()
                             ->all();
        $shopItems = ArrayHelper::map($shopItems, 'id', 'xml_id');
        $shopItemCharDataArray = [];
        $i = 0;
        foreach ($this->getFormattedArrayParams() as $charId => $items) {
            foreach ($items as $charTitle => $shopItemXmlId) {
                if (array_key_exists($charId, $charsArray)) {
                    $charValueId = 0;
                    $quoteCharTitle = Yii::$app->db->quoteValue($charTitle);
                    if (array_key_exists($quoteCharTitle, $charsArray[$charId])) {
                        $charValueId = $charsArray[$charId][$charTitle];
                    }
                    if (array_key_exists($charTitle, $charsArray[$charId])) {
                        $charValueId = $charsArray[$charId][$charTitle];
                    }
                    if ($charValueId != 0) {
                        $shopItemsIdsArray = array_flip(array_intersect($shopItems, $shopItemXmlId));
                        if (!empty($shopItemsIdsArray)) {
                            $shopItemCharDataArray[$charValueId] = $shopItemsIdsArray;
                            $i++;
                        }
                    }
                }
            }
        }
        return $shopItemCharDataArray;
    }

    public function makeAndRunShopItemCharSql($data)
    {
        $sql = "SET NAMES utf8 COLLATE utf8_general_ci;";
        $sql .= "\n INSERT IGNORE INTO `shop_item_char` (char_value_id,shop_item_id,sync_status) VALUES ";
        $i = 0;
        $total = 0;
        foreach ($data as $charId => $shopItems) {
            foreach ($shopItems as $shopItemXmlId => $shopItemId) {
                $values = "($charId,$shopItemId,0)";
                if ($i == 1500) {
                    $sql .= ";\n INSERT IGNORE INTO `shop_item_char` (char_value_id,shop_item_id,sync_status) VALUES $values, ";
                    $i = 0;
                } else {
                    if ($i > 0) {
                        $sql .= ",";
                    }
                    $sql .= $values;
                    $i++;
                }
                $total++;
            }
        }

        $this->printR(" printing shop_item_char.sql\n");

        $sqlFile = $this->importDir . 'shop_item_char.sql';
        file_put_contents($sqlFile, $sql);
        $this->runSql($sqlFile);
        return $total;
    }

    public function getCharsToAdd($oldChars)
    {
        $this->printR("\n Comparing params \n");
        $charsToAdd = [];
        $formatted = $this->getFormattedArrayParams();
        $i = 0;
        $existsCharsTitlesArray = [];
        foreach ($this->getExistsCharValues() as $data) {
            $existsCharsTitlesArray[$data['char_id']][] = $data['title'];
            if ($data['title_ru']) {
                $existsCharsTitlesArray[$data['char_id']][] = $data['title_ru'];
            }
            $i++;
        }

        $xmlCharTitles = [];
        foreach ($formatted as $charId => $data) {
            foreach ($data as $charTitle => $shopItemsIds) {
                $xmlCharTitles[$charId][] = $charTitle;
                $i++;
            }
        }

        $category_chars = [];
        foreach ($xmlCharTitles as $charId => $charTitles) {
            if (array_key_exists($charId, $existsCharsTitlesArray)) {
                if ($tmp = array_diff($charTitles, $existsCharsTitlesArray[$charId])) {
                    $category_chars[$charId] = $tmp;
                    $i++;
                }
            } else {
                $category_chars[$charId] = $charTitles;
            }
        }


        foreach ($category_chars as $charId => $charTitles) {
            foreach ($charTitles as $charTitle) {
                $charsToAdd[$charId][$charTitle] = $formatted[$charId][$charTitle];
                $i++;
            }
        }

        return $charsToAdd;
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

    private function printR(string $string)
    {
        print_r($string);
    }
}
