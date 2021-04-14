<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 05.02.18
 * Time: 13:19
 */

namespace backend\widgets;

use dmstr\widgets\Menu;

class AdminMenu extends Menu
{
    public $activeUrl;
    private $noDefaultAction;
    private $noDefaultRoute;
/*
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
            if (count($currentUrlParts) > 1 && $currentUrlParts[0] == '') {
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
    }*/
}
