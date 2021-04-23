<?php
/**
 * Created by topalek
 * Date: 23.04.2021
 * Time: 10:08
 */

namespace frontend\widgets;


use common\modules\catalog\models\Category;
use common\modules\catalog\models\PropertyCategory;
use Yii;
use yii\widgets\Menu;

class SideNavMenu extends Menu
{
    public $submenuTemplate = "\n<ul class='submenu'>\n{items}\n</ul>\n";
    public $activateParents = true;
    public $labelTemplate = '<span>{label}</span>';
    public $options = [
        'class' => 'sidebar-menu-widget',
    ];
    public $itemOptions = [
        'class' => 'sidebar-menu-widget-title',
    ];
    /**
     * @var mixed
     */
    private $activeUrl;

    public function init()
    {
        Yii::$app->request->setQueryParams(['prop' => 123]);

        $category = Category::roots();
        $catitems = [];

        foreach ($category as $cat) {
            $catitems[] = [
                'url'   => $cat->seoUrl,
                'label' => $cat->getMlTitle(),
            ];
        }
        $propCat = PropertyCategory::find()->all();
        $propCatitems = [];

        foreach ($propCat as $cat) {
            $propCatitems[] = [
                'url'   => "?prop=" . $cat->id,
                'label' => $cat->getMlTitle(),
            ];
        }
        $this->items = [
            [
                'label'  => \Yii::t('shop', 'Категории'),
                'items'  => $catitems,
                'active' => true,
            ],
            [
                'label'  => \Yii::t('shop', 'Характеристики'),
                'items'  => $propCatitems,
                'active' => true,
            ],
        ];
    }

    protected function isItemActive($item)
    {
        $url = trim(Yii::$app->request->url, '/');
        if ($this->activeUrl) {
            $url = $this->activeUrl;
        }
        $url = explode('/', $url);
        $current_url = $url[0] . '/' . (isset($url[1]) ? $url[1] . '/' : '');

        if (isset($item['url']) && $current_url != '/') {
            $itemUrl = (is_array($item['url'])) ? $item['url'][0] : $item['url'];
            $currentUrlParts = explode($itemUrl, Yii::$app->request->url);
            if ($item['url'] == $current_url) {
                return true;
            }
            if (count($currentUrlParts) > 1 && $currentUrlParts[0] == '' && $currentUrlParts[1] == '') {
                return true;
            }
        }

        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route == $this->activeUrl) {
                return true;
            }
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
            }
            $route = ltrim($route, '/');
            if ($route != $this->route && $route !== $this->noDefaultRoute && $route !== $this->noDefaultAction) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }


}
