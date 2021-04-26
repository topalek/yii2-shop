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
            $html .= Html::tag(
                'span',
                $prop->getMlTitle(),
                [
                    'class'          => in_array($prop->id, $this->filters) ? 'filter active' : 'filter',
                    'data-filter-id' => $prop->id,
                ]
            );
        }
        $html .= Html::endTag('div');
        return $html;
    }
}
