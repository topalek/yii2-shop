<?php

use yii\helpers\BaseHtml;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$userImg = '/img/undraw_profile.svg';
?>

<header class="main-header">

    <?= Html::a(
        '<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>',
        Yii::$app->homeUrl,
        ['class' => 'logo']
    ) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <?= BaseHtml::a(
                '<i class="fa fa-globe"></i>',
                Yii::$app->params['frontendUrl'],
                ['class' => 'navbar-custom-menu-link', 'title' => 'Перейти на сайт', 'target' => '_blank']
            ) ?>
            <?= BaseHtml::a(
                '<i class="fa fa-sign-out"></i>',
                ['/site/logout'],
                ['class' => 'navbar-custom-menu-link', 'title' => 'Выход', 'data-method' => 'post']
            ) ?>
        </div>
    </nav>
</header>
