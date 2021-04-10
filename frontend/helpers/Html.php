<?php

namespace frontend\helpers;

use common\components\BaseModel;
use ReflectionClass;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: saniok
 * Date: 13.11.18
 * Time: 16:23
 */
class Html extends \yii\helpers\Html
{
    public const TABLET_IMG_SIZE = 't';
    public const MOBILE_IMG_SIZE = 'm';
    public const DESKTOP_IMG_SIZE = 'd';
    public const ANY_DEV_IMG_SIZE = 'a';

    public static function activeStyledCheckBox($model, $attribute, $options = [])
    {
        /**
         * @var $model Model
         */
        $inputOptions = $options['inputOptions'] ?? [];
        $labelOptions = $options['labelOptions'] ?? [];
        $template = $options['template'] ?? null;
        $containerFieldClass = 'field-' . str_replace('_', '-', $attribute);
        $id = $inputOptions['id'] ?? Inflector::camel2id((new ReflectionClass($model))->getShortName());
        $checked = $model->{$attribute} == 1;
        $inputOptions['id'] = $id;
        $inputEmulatorTag = static::tag('span', '', ['class' => 'input-emulator']);
        $label = $model->getAttributeLabel($attribute);
        $wrapLabel = $options['wrapLabel'] ?? true;
        if (isset($options['wrapLabel'])) {
            unset($options['wrapLabel']);
        }
        if ($wrapLabel) {
            $label = static::tag('span', $label, ['class' => 'label-text']);
        }
        $html = static::beginTag('div', ['class' => 'check-box-container form-group ' . $containerFieldClass]);
        $input = static::checkbox(static::getInputName($model, $attribute), $checked, $inputOptions);
        $label = static::label($inputEmulatorTag . $label, $id, $labelOptions);
        if ($template) {
            $template = str_replace('{input}', $input, $template);
            $template = str_replace('{label}', $label, $template);
            $html .= $template;
        } else {
            $html .= $input;
            $html .= $label;
        }
        $html .= static::endTag('div');
        return $html;
    }

    /**
     * @param       $name
     * @param bool  $checked
     * @param array $options
     *
     * @return string
     */
    public static function styledCheckBox($name, $checked = false, $options = [])
    {
        $inputId = $options['id'] ?? 'checkbox-' . substr(md5(microtime()), 0, 5);
        $options['id'] = $inputId;
        $label = $options['label'] ?? null;
        $options['label'] = null;
        $labelTitle = $options['labelTitle'] ?? null;
        $inputEmulatorTagOptions = ['class' => 'input-emulator'];
        if ($labelTitle) {
            $inputEmulatorTagOptions['title'] = $labelTitle;
        }
        $inputEmulatorTag = static::tag('span', '', $inputEmulatorTagOptions);

        $wrapLabel = $options['wrapLabel'] ?? true;
        if (isset($options['wrapLabel'])) {
            unset($options['wrapLabel']);
        }
        if ($wrapLabel) {
            $label = static::tag('span', $label, ['class' => 'label-text']);
        }
        $html = static::beginTag('div', ['class' => 'check-box-container']);
        $html .= static::checkbox($name, $checked, $options);
        $html .= static::label(
            $inputEmulatorTag . $label,
            $inputId,
            $options['labelOptions'] ?? []
        );
        $html .= static::endTag('div');
        return $html;
    }

    public static function activeStyledRadioList($model, $attribute, $items, $options = [])
    {
        /**
         * @var $model Model
         */
        $inputOptions = $options['inputOptions'] ?? [];
        $labelOptions = $options['labelOptions'] ?? [];
        $template = $options['template'] ?? null;
        $containerFieldClass = 'field-' . str_replace('_', '-', $attribute);
        $id = $inputOptions['id'] ?? Inflector::camel2id((new ReflectionClass($model))->getShortName());
        $inputOptions['id'] = $id;
        $html = static::beginTag('div', ['class' => 'form-group ' . $containerFieldClass]);
        $html .= static::styledRadioList(
            static::getInputName($model, $attribute),
            $model->{$attribute},
            $items,
            $options
        );
        $html .= static::endTag('div');
        return $html;
    }

    public static function styledRadioList($name, $selection = null, $items = [], $options = [])
    {
        $html = static::beginTag('div', ['class' => 'radio-list-container']);
        $inputId = $options['id'] ?? 'radio-' . substr(md5(microtime()), 0, 5);
        $options['id'] = $inputId;
        $options['name'] = $name;
        foreach ($items as $value => $label) {
            $checked = false;
            if (is_array($selection) && in_array($value, $selection) || $selection == $value) {
                $checked = true;
            }
            $options['label'] = $label;
            $options['value'] = $value;
            $html .= static::styledRadio($name, $checked, $options);
        }
        $html .= static::endTag('div');
        return $html;
    }

    public static function styledRadio($name, $checked, $options = [])
    {
        $inputId = $options['id'] ?? 'radio-' . $name . ($options['inputOptions']['value'] ?? rand(0, 10000000));
        $inputId = str_replace(['[', ']'], ['-', ''], $inputId);
        $label = $options['label'] ?? null;
        $labelHtml = null;
        if (isset($options['labelHtml'])) {
            $labelHtml = $options['labelHtml'];
            unset($options['labelHtml']);
        }
        $options['label'] = null;
        $labelOptions = [];
        if (isset($options['labelOptions'])) {
            $labelOptions = $options['labelOptions'];
            unset($options['labelOptions']);
        }
        if (isset($options['inputOptions'])) {
            $options = $options['inputOptions'];
            unset($options['inputOptions']);
        }
        $options['id'] = $inputId;
        $inputEmulatorTag = static::tag('span', '', ['class' => 'input-emulator']);
        $html = static::beginTag('div', ['class' => 'radio-input-container']);
        $html .= static::radio($name, $checked, $options);
        if ($labelHtml) {
            $html .= static::label(
                $inputEmulatorTag . $labelHtml,
                $inputId,
                $labelOptions
            );
            unset($options['labelHtml']);
        } else {
            $html .= static::label(
                $inputEmulatorTag . static::tag('span', $label, ['class' => 'label-text']),
                $inputId,
                $labelOptions
            );
        }
        $html .= static::endTag('div');
        return $html;
    }

    public static function productRating($count, $url)
    {
        return static::tag(
            'span',
            Yii::t(
                'review',
                '{n, plural, =1{# відгук} one{# відгук} few{# відгуки} many{# вікгуків} other{# відгуків}}',
                ['n' => $count]
            ),
            [
                'class'    => 'review-link go-to-url-btn',
                'data-url' => Url::to([$url, 'scrollToElement' => 'shop-item-reviews']),
            ]
        );
    }

    /**
     * @param array $sources
     * @param array $imgTag
     *
     * @return string
     */
    public static function picture(array $sources, array $imgTag = [], array $options = [])
    {
        $sourceTemplate = '<source srcset="{srcset}" media="{media}">';

        $result[] = self::beginTag('picture', $options);
        foreach ($sources as $source) {
            $result[] = strtr(
                $sourceTemplate,
                ['{srcset}' => $source['srcset'], '{media}' => $source['media'] ?? null]
            );
        }
        if (!empty($imgTag)) {
            $result[] = self::img($imgTag['src'], $imgTag['options'] ?? []);
        }
        $result[] = self::endTag('picture');
        return implode('', $result);
    }

    /**
     * example
     * Html::img(
     *      $src,
     *      [
     *          'lazyLoad' => false,                            "lazy-load" class will be NO added. Default 'lazyLoad' => true
     *                                                          To srcset attribute of image will be placed $src
     *
     *          'responsive' => false,                          "img-responsive" class will be NO added. Default 'responsive' => true,
     *          'size'=> [
     *                Html::MOBILE_IMG_SIZE  => '410xauto',     img will bee resized to 410px width proportional
     *                Html::ANY_DEV_IMG_SIZE => '410x310',      img will bee resized to 410px width and 310px height with white stripes
     *                Html::DESKTOP_IMG_SIZE => '410x310xc',    img will bee resized to 410px width and 310px and Cropped
     *          ]
     *      ]
     * )
     *
     * @param array|string $src
     * @param array        $options
     *
     * @return string
     */
    public static function img($src, $options = [])
    {
        $defaultWidth = 200;
        $srcset = null;
        $class = $options['class'] ?? null;
        $lazyLoad = $options['lazyLoad'] ?? true;
        $responsive = $options['responsive'] ?? true;
        $size = ArrayHelper::remove($options, 'size');

        $src = BaseModel::moduleUploadsDir() . str_replace(BaseModel::moduleUploadsDir(), '', '/' . ltrim($src, '/'));
        if ($size) {
            $width = $height = null;
            $crop = false;
            if (Yii::$app->getView()->params['isPhone'] && isset($size[Html::MOBILE_IMG_SIZE])) {
                $dimentions = explode('x', $size[Html::MOBILE_IMG_SIZE]);
                if (count($dimentions) == 3) {
                    [$width, $height, $crop] = $dimentions;
                } elseif (count($dimentions) == 2) {
                    [$width, $height] = $dimentions;
                } else {
                    [$width] = $dimentions;
                }
            }
            if (Yii::$app->getView()->params['isTablet'] && isset($size[Html::TABLET_IMG_SIZE])) {
                $dimentions = explode('x', $size[Html::TABLET_IMG_SIZE]);
                if (count($dimentions) == 3) {
                    [$width, $height, $crop] = $dimentions;
                } elseif (count($dimentions) == 2) {
                    [$width, $height] = $dimentions;
                } else {
                    [$width] = $dimentions;
                }
            }
            if (!Yii::$app->getView()->params['isMobile'] && isset($size[Html::DESKTOP_IMG_SIZE])) {
                $dimentions = explode('x', $size[Html::DESKTOP_IMG_SIZE]);
                if (count($dimentions) == 3) {
                    [$width, $height, $crop] = $dimentions;
                } elseif (count($dimentions) == 2) {
                    [$width, $height] = $dimentions;
                } else {
                    [$width] = $dimentions;
                }
            }
            if (isset($size[Html::ANY_DEV_IMG_SIZE])) {
                if (strpos($size[Html::ANY_DEV_IMG_SIZE], 'x') !== false) {
                    $dimentions = explode('x', $size[Html::ANY_DEV_IMG_SIZE]);
                    if (count($dimentions) == 3) {
                        [$width, $height, $crop] = $dimentions;
                    } elseif (count($dimentions) == 2) {
                        [$width, $height] = $dimentions;
                    } else {
                        [$width] = $dimentions;
                    }
                } else {
                    $width = $height = $size[Html::ANY_DEV_IMG_SIZE];
                }
            }
            if ($width) {
                $srcset = makeDynamicImageThumbUrl($src, $width, $height, boolval($crop));
            } else {
                $width = $height = $defaultWidth;
            }
        } else {
            if (getImgSize($src)) {
                [$width, $height] = getImgSize($src);
            } else {
                $width = $options['width'] ?? $defaultWidth;
                $height = $options['height'] ?? $defaultWidth;
            }
        }
        $options['width'] = $width;
        $options['height'] = $height;

        if ($class != null) {
            $class .= ' ';
        }
        $class .= 'lazy-load';
        $options['srcset'] = lazyLoadImgUrl() . " " . $width . "w";

        if (!isset($options['data-srcset'])) {
            $options['data-srcset'] = $srcset ? $srcset : $src . " " . $width . "w";
        }

        $options['decoding'] = 'async';

        if ($responsive) {
            $class .= ' img-responsive';
        }

        $options['class'] = $class;
        return parent::img($src, $options);
    }
}
