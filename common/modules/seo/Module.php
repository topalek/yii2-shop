<?php

namespace common\modules\seo;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\seo\controllers';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    /** дефолтна конфігурація модуля
     *
     * @return array
     */
    public function getDefaultConfig()
    {
        return [
            //загальні дані
            'title'         => 'SEO',
            'description'   => 'SEO module',

            //додаткові
            'in_admin_menu' => 1, //показувати в адмінці
            'link_allow'    => 0, //доступність для прямого посилання
            'search_allow'  => 0, //доступність для пошуку

            //посилання для адмін меню (назва блока береться з параметра "title")
            'admin_menu'    => [
                ['label' => 'SEO', 'url' => ['/seo/admin/index']],
            ],

            //параметри модуля, при ініціалізації будуть записані в БД з можливістю редагування
            //параметри
            //name - назва параметру
            //value - значення параметру
            //type - тип (string, text, boolean, file, integer)
            'params'        => [
                ['name' => 'title', 'value' => 'my yii site', 'type' => 'string'],
                ['name' => 'description', 'value' => 'my yii site desc', 'type' => 'string'],
                ['name' => 'keywords', 'value' => 'my yii site keys', 'type' => 'string'],
            ],

            //правила для СЕО модуля, будуть записані в БД при ініціалізації
            'url_rules'     => false,
        ];
    }
}
