<?php
if (strpos($_SERVER['REQUEST_URI'], '/thumbs/') !== false) {
    try {
        require_once(dirname(dirname(__DIR__)) . '/common/components/DynamicImgThumbMaker.php');
        new DynamicImgThumbMaker();
    } catch (Exception $exception) {
        print_r($exception);
    }
    exit;
}
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();
