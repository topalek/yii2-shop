<?php

/**
 * Created by topalek
 * Date: 14.04.2021
 * Time: 14:46
 */

/* @var $this \yii\web\View */
/* @var $navItems array */
/* @var $langList array */

/* @var $currentLang string */

use common\modules\shop\models\Cart;
use frontend\widgets\MenuWidget;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- Offcanvas Menu Begin -->
<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <?= Nav::widget(
                [
                    'options' => ['class' => 'mobile-nav'],
                    'items'   => $navItems,
                ]
            ) ?>
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="#" class="search-switch"><img src="/img/icon/search.png" alt=""></a>
        < <a href="<?= Url::to(['/shop/default/view-cart']) ?>" id="cart">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <span><?= Cart::getItemsCount() ?></span></a>
        <?php
        //$langList = Translate::getLangList();
        if (count($langList) > 1) :?>
            <div class="lang">
                <?php
                foreach ($langList as $langPrefix => $lang) {
                    if ($langPrefix == $currentLang) {
                        echo Html::tag(
                            'span',
                            $langPrefix,
                            ['class' => 'lang-item', 'title' => $lang['label']]
                        );
                    } else {
                        echo Html::a(
                            $langPrefix,
                            $lang['url'],
                            ['class' => 'lang-item', 'title' => $lang['label']]
                        );
                    }
                } ?>
            </div>
        <?php
        endif; ?>
    </div>
    <div id="mobile-menu-wrap"></div>
</div>
<!-- Offcanvas Menu End -->

<!-- Header Section Begin -->
<header class="header">
    <div class="container">
        <div class="wrap">
            <div class="header__logo">
                <?= Html::a(Html::img("/img/logo.png"), ['/']) ?>
            </div>
            <nav class="header__menu mobile-menu">
                <?= MenuWidget::widget(
                    [
                        'options' => ['class' => 'header-nav'],
                        'items'   => $navItems,
                    ]
                ) ?>
            </nav>
            <div class="header__nav__option">
                <a href="#" class="search-switch">
                    <img src="/img/icon/search.png" alt="">
                </a>
                <a href="<?= Url::to(['/shop/default/view-cart']) ?>" id="cart">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    <span><?= Cart::getItemsCount() ?></span></a>
                <?php
                //$langList = Translate::getLangList();
                if (count($langList) > 1) :?>
                    <div class="lang">
                        <?php
                        foreach ($langList as $langPrefix => $lang) {
                            if ($langPrefix == $currentLang) {
                                echo Html::tag(
                                    'span',
                                    $langPrefix,
                                    ['class' => 'lang-item', 'title' => $lang['label']]
                                );
                            } else {
                                echo Html::a(
                                    $langPrefix,
                                    $lang['url'],
                                    ['class' => 'lang-item', 'title' => $lang['label']]
                                );
                            }
                        } ?>
                    </div>
                <?php
                endif; ?>
            </div>
            <div class="canvas__open"><i class="fa fa-bars"></i></div>
        </div>
    </div>
</header>
<!-- Header Section End -->
