<?php

use common\components\BaseUrlManager;
use common\modules\catalog\CatalogModule;
use common\modules\image\ImageModule;

return [
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules'    => [
        'catalog'   => [
            'class' => CatalogModule::class,
        ],
        'htmlBlock' => [
            'class' => common\modules\htmlBlock\Module::class,
        ],
        'image'     => [
            'class' => ImageModule::class,
        ],
        'page'      => [
            'class' => common\modules\page\Module::class,
        ],
        'params'    => [
            'class' => common\modules\params\Module::class,
        ],
        'seo'       => [
            'class' => common\modules\seo\Module::class,
        ],
        'shop'      => [
            'class' => common\modules\shop\ShopModule::class,
        ],
        'translate' => [
            'class' => common\modules\translate\TranslateModule::class,
        ],
        'user'      => [
            'class' => common\modules\user\UserModule::class,
        ],
        'search'    => [
            'class' => 'app\modules\search\SearchModule',
        ],
    ],
    'components' => [
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager'   => [
            'class' => BaseUrlManager::class,
        ],
        'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'i18n'         => [
            'translations' => [
                '*' => [
                    'class'              => 'yii\i18n\DbMessageSource',
                    'forceTranslation'   => true,
                    'sourceLanguage'     => 'ru',
                    'enableCaching'      => true,
                    'sourceMessageTable' => 'source_translate',
                    'messageTable'       => 'translate',
                    'cachingDuration'    => 86400,
                ],
            ],
        ],
    ],
];
