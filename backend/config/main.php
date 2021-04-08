<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-backend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap'           => ['log'],
    'language'            => 'ru',
    'modules'             => [],
    'components'          => [
        'request'      => [
            'csrfParam'           => '_csrf',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user'         => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => [
                'name'     => '_identity-ecommerce',
                'httpOnly' => true,
                'domain'   => $params['cookieDomain'],
            ],
        ],
        'formatter'    => [
            'dateFormat'        => 'dd.MM.yyyy',
            'datetimeFormat'    => 'php:d.m.Y H:i',
            'decimalSeparator'  => ',',
            'thousandSeparator' => ' ',
            'currencyCode'      => 'UAH',
        ],
        'session'      => [
            'name'         => 'ecommerce_session',
            'cookieParams' => [
                'httpOnly' => true,
                'domain'   => $params['cookieDomain'],
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                yii\bootstrap4\BootstrapAsset::class => false,
            ],
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
            ],
        ],
    ],
    'params'              => $params,
];
