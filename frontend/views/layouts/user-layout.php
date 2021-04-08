<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
</head>
<body class="user-layout">
<?php
$this->beginBody() ?>

<!-- Offcanvas Menu Begin -->
<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <?= $this->render('parts/_top_links') ?>
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="#" class="search-switch"><img src="/img/icon/search.png" alt=""></a>
        <a href="#"><img src="/img/icon/heart.png" alt=""></a>
        <a href="#"><img src="/img/icon/cart.png" alt=""> <span>0</span></a>
        <div class="price">$0.00</div>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Free shipping, 30-day return or refund guarantee.</p>
    </div>
</div>
<!-- Offcanvas Menu End -->

<!-- Header Section Begin -->
<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header__top__left">
                        <?= Html::a(Html::img("/img/logo.png"), ['/'], ['class' => 'logo-link']) ?>
                    </div>
                </div>
                <div class="col">
                    <div class="header__top__right">
                        <div class="header__top__links">
                            <?= $this->render('parts/_top_links') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header Section End -->
<div class="container">
    <div class="row pt-3">
        <div class="col-lg-12">
            <?= Alert::widget() ?>
        </div>
    </div>
</div>
<div class="container">
    <div class="row pt-3">
        <div class="col-lg-3">
            <?= $this->render('../profile/_sidebar') ?>
        </div>
        <div class="col-lg-9">
            <?= $content ?>
        </div>
    </div>
</div>

<?= $this->render('../layouts/parts/footer') ?>
<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>

<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>
