<?php

namespace common\components;

/**
 * Created by topalek
 * Date: 23.04.2021
 * Time: 14:59
 *
 * Class SwitchColumn
 *
 * @package common\common
 */

use yii\grid\DataColumn;
use yii\helpers\Html;

class SwitchColumn extends DataColumn
{
    public $visible = true;
    public $contentAsText = false;
    public $trueText = 'Да';
    public $falseText = 'Нет';
    public $format = 'raw';
    public $filter = [
        1 => 'Да',
        0 => 'Нет',
    ];

    /**
     *
     */
    public function init()
    {
        $this->headerOptions = ['class' => 'switch-column', 'style' => 'width:55px'];
        parent::init();
    }

    /**
     * @param $model
     * @param $key
     * @param $index
     *
     * @return string
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->contentAsText) {
            return ($model->{$this->attribute} == 1) ? $this->trueText : $this->falseText;
        } else {
            $class = ($model->{$this->attribute} == 1) ? 'fa-check-square-o' : 'fa-square-o';
            $class = 'switch-state-btn fa ' . $class;
            $disabled = null;

            return Html::tag(
                'i',
                '',
                [
                    'class'      => $class,
                    'data-field' => $this->attribute,
                    'data-url'   => \yii\helpers\Url::to(['switch-state']),
                    'data-id'    => $model->id,
                    'data-class' => get_class($model),
                    'data-pjax'  => 0,
                    'disabled'   => $disabled,
                ]
            );
        }
    }
}
