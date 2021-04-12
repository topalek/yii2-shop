<?php

namespace frontend\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

class MenuWidget extends Menu
{
    public $items = [];
    public $dropDownContainer = false;

    public function run()
    {
        $this->options = ['class' => 'list-inline'];
        $this->encodeLabels = false;
        parent::run();
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
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $itemSubMenuTemplate = null;
                if (isset($item['itemSubMenuTemplate'])) {
                    $itemSubMenuTemplate = $item['itemSubMenuTemplate'];
                }
                $submenuTemplate = ArrayHelper::getValue(
                    $item,
                    'submenuTemplate',
                    ($itemSubMenuTemplate) ? $itemSubMenuTemplate : $this->submenuTemplate
                );
                $menu .= strtr(
                    $submenuTemplate,
                    [
                        '{items}' => $this->renderItems($item['items']),
                    ]
                );
            }
            if ($tag === false) {
                $lines[] = $menu;
            } else {
                $lines[] = Html::tag($tag, $menu, $options);
            }
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item)
    {
        if (isset($item['url']) && Yii::$app->request->url != $item['url']) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr(
                $template,
                [
                    '{url}'   => Url::to($item['url']),
                    '{label}' => (isset($item['content'])) ? $item['content'] : $item['label'],
                ]
            );
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            if (isset($item['dropDownContainer']) && $item['dropDownContainer'] == true) {
                return Html::tag(
                    'span',
                    $item['label'],
                    [
                        'id'            => $item['dropContainerId'],
                        'class'         => 'dropdown-toggle',
                        'data-toggle'   => 'dropdown',
                        'aria-expanded' => true,
                        'aria-haspopup' => true,
                    ]
                );
            } else {
                return strtr(
                    $template,
                    [
                        '{label}' => (isset($item['content'])) ? $item['content'] : Html::tag('span', $item['label']),
                    ]
                );
            }
        }
    }

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
