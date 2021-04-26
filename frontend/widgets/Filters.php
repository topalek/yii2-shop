<?php
/**
 * Created by topalek
 * Date: 26.04.2021
 * Time: 9:57
 */

namespace frontend\widgets;


use common\modules\catalog\models\Property;
use common\modules\catalog\models\PropertyCategory;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Menu;

class Filters extends Menu
{
    public $linkTemplate = '<span class="filter" data-filter-id="{id}">{label}</span>';
    private array $filters = [];
    private $categoryId;
    /**
     * @var array|PropertyCategory[]|mixed
     */
    private $filterCategories;
    /**
     * @var array|PropertyCategory[]|mixed
     */
    private $activeProperties;

    public function init()
    {
        $this->options = [
            'tag'   => 'div',
            'class' => 'filter-container',
        ];

        $this->filterCategories = PropertyCategory::find()->forFilters()->with('properties')->all();
        $route = Yii::$app->request->resolve();
        $route = array_pop($route);
        $this->categoryId = ArrayHelper::getValue($route, 'id');
        $this->filters = explode(',', ArrayHelper::getValue($route, 'filter'));
        $sql = "select property_id from {{%product_property}} where product_id in (select id from product where category_id= {$this->categoryId})";
        $this->activeProperties = Yii::$app->db->createCommand($sql)->queryAll();
        if ($this->activeProperties) {
            $this->activeProperties = ArrayHelper::getColumn($this->activeProperties, 'property_id');
        }
        $items = [];
        foreach ($this->filterCategories as $filterCategory) {
            $items[$filterCategory->id] = [
                'label' => $filterCategory->getMlTitle(),
                'id'    => $filterCategory->id,
                'items' => $filterCategory->properties,
            ];
        }
        $this->items = $items;
    }

    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if (!empty($this->items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');

            echo Html::tag($tag, $this->renderItems($this->items), $options);
        }
    }

    /**
     * Recursively renders the menu items (without the container tag).
     *
     * @param array $items the menu items to be rendered recursively
     *
     * @return string the rendering result
     */
    protected function renderItems($items)
    {
        $lines = [];
        foreach ($items as $item) {
            $filters = $this->renderItem($item);
            $lines[] = Html::tag('div', $filters, ['class' => 'filter-box']);
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item)
    {
        $propIds = ArrayHelper::getColumn($item['items'], 'id');
        $isActive = array_intersect($this->filters, $propIds);
        $isActive = !empty($isActive);
        $isActive = true;

        $html = Html::tag(
            'div',
            $item['label'],
            [
                'class'   => $isActive ? 'filter-box-title active' : 'filter-box-title',
                'data-id' => $item['id'],
            ]
        );

        $html .= Html::beginTag(
            'div',
            [
                'class' => 'filter-box-body',
                'style' => $isActive ? '' : 'display:none',
            ]
        );
        /** @var Property $prop */
        foreach ($item['items'] as $prop) {
            $class = 'filter';
            if (in_array($prop->id, $this->filters)) {
                $class .= ' active';
            }
            if (!in_array($prop->id, $this->activeProperties)) {
                $class .= ' disabled';
            }
            $html .= Html::tag(
                'span',
                $prop->getMlTitle(),
                [
                    'class'          => $class,
                    'data-filter-id' => $prop->id,
                ]
            );
        }
        $html .= Html::endTag('div');
        return $html;
    }
}
