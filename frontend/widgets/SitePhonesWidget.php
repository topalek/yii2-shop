<?php
/**
 * Created by topalek
 * Date: 02.04.2021
 * Time: 11:51
 */

namespace frontend\widgets;


use common\modules\params\models\Params;
use yii\base\Widget;
use yii\helpers\Html;

class SitePhonesWidget extends Widget
{
    public $icon = true;
    public $links = true;
    public $container = 'div';
    public $containerOptions = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $phones = Params::getSitePhones();
        $html = '';
        if ($phones) {
            $phones = explode(',', $phones);
            $phones = array_map('trim', $phones);

            if ($this->icon) {
                $html .= '<i class="fa fa-phone"></i>' . PHP_EOL;
            }
            foreach ($phones as $phone) {
                if ($this->links) {
                    $html .= '<a href="tel:' . str_replace(
                            [' ', '(', ')', '-'],
                            '',
                            $phone
                        ) . '">' . $phone . '</a>' . PHP_EOL;
                } else {
                    $html .= '<span>' . $phone . '</span>' . PHP_EOL;
                }
            }
            if ($this->container) {
                $html = Html::tag($this->container, $html, $this->containerOptions);
            }
        }

        return $html;
    }

}
