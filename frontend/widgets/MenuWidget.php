<?php

namespace frontend\widgets;

use Yii;
use yii\widgets\Menu;

class MenuWidget extends Menu
{

    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
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

        if (isset($item['url'])) {
            $requestUrl = strtok(Yii::$app->request->url, '?');

            if (Yii::$app->request->url == $item['url'] || $requestUrl == $item['url']) {
                return true;
            }

            $requestUrlArray = explode('/', $requestUrl);

            if (is_array($requestUrlArray)) {
                $urlWithoutSlash = substr($item['url'], 1);
                var_dump($urlWithoutSlash);
                if ($urlWithoutSlash != null && in_array($urlWithoutSlash, $requestUrlArray)) {
                    return true;
                } elseif ($urlWithoutSlash == $requestUrlArray[1]) {
                    return true;
                } elseif (isset($requestUrlArray[2])) {
                    if ($urlWithoutSlash == $requestUrlArray[2]) {
                        return true;
                    } elseif ($urlWithoutSlash == $requestUrlArray[1] . '/' . $requestUrlArray[2]) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
