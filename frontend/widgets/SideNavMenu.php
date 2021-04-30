<?php
/**
 * Created by topalek
 * Date: 23.04.2021
 * Time: 10:08
 */

namespace frontend\widgets;


use common\modules\catalog\models\Category;
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

    public function init()
    {
        $categories = Category::roots();
        $catitems = [];

        foreach ($categories as $cat) {
            $catitems[$cat->id] = [
                'url'       => $cat->seoUrl,
                'id'        => $cat->id,
                'parent_id' => $cat->parent_id,
                'label'     => $cat->getMlTitle(),
            ];
        }

        $this->items = [
            [
                'label'  => \Yii::t('shop', 'Категории'),
                'items'  => $catitems,
                'active' => true,
            ],
        ];
    }

    protected function isItemActive($item)
    {
        $activeUrl = trim(Yii::$app->request->pathInfo, '/');
        $url = explode('/', $activeUrl);
        $current_url = $url[0] . '/' . (isset($url[1]) ? $url[1] . '/' : '');

        if (isset($item['url']) && $current_url != '/') {
            $itemUrl = (is_array($item['url'])) ? $item['url'][0] : $item['url'];
            $currentUrlParts = explode($itemUrl, Yii::$app->request->pathInfo);
            if ($item['url'] == $current_url) {
                return true;
            }
            if (count($currentUrlParts) > 1 && $currentUrlParts[0] == '' && $currentUrlParts[1] == '') {
                return true;
            }

            if ($item['url'] == '/' . $activeUrl) {
                return true;
            }
        }

        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route == $activeUrl) {
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

    private function buildTree($categories)
    {
        $tree = [];
        foreach ($categories as $id => &$category) {
            if (!$category['parent_id']) {
                $tree[$id] = &$category;
            } else {
                $categories[$category['parent_id']]['items'][$id] = &$category;
            }
        }
        return $tree;
    }
}
