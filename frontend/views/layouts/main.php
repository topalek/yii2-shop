<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\components\BaseModel;
use common\components\BaseUrlManager;
use common\modules\translate\models\Translate;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\widgets\SitePhonesWidget;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

$currentLang = Yii::$app->language;
$urlWithoutLangPrefix = BaseUrlManager::getUrlWithoutLangPrefix();

$langList = [];
foreach (Translate::getLangList() as $lang => $langTitle) {
    $langList[$lang] = [
        'label' => $langTitle,
        'url'   => BaseModel::DEFAULT_LANG === $lang ? '/' . ltrim(
                $urlWithoutLangPrefix,
                '/'
            ) : '/' . $lang . $urlWithoutLangPrefix,
    ];
}
$navItems = [
    [
        'label' => Yii::t('site', 'Главная'),
        'url'   => Yii::$app->homeUrl,
    ],
    [
        'label' => Yii::t('site', 'Каталог'),
        'url'   => ['/catalog'],
    ],
    [
        'label' => Yii::t('site', 'О нас'),
        'url'   => ['/o_nas'],
    ],
    [
        'label' => Yii::t('site', 'Контакты'),
        'url'   => ['/kontakty'],
    ],
    [
        'label' => Yii::t('site', 'Доставка и оплата'),
        'url'   => ['/dostavka_i_oplata'],
    ],
];
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
<body>
<?php
$this->beginBody() ?>
<div class="wrap">
    <!-- Page Preloder -->
    <!--<div id="preloder">
        <div class="loader"></div>
    </div>-->

    <?= $this->render('parts/_header', compact('navItems', 'langList', 'currentLang')) ?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <?= Breadcrumbs::widget(
                            [
                                'links'              => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                'tag'                => 'div',
                                'options'            => ['class' => 'breadcrumb__links'],
                                'itemTemplate'       => "{link}\n",
                                'activeItemTemplate' => '<span>{link}</span>',
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?= Alert::widget() ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- Footer Section Begin -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__about">
                        <div class="footer__logo">
                            <a href="#"><img src="/img/footer-logo.png" alt=""></a>
                        </div>
                        <p>The customer is at the heart of our unique business model, which includes design.</p>
                        <a href="#"><img src="/img/payment.png" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                    <div class="footer__widget">

                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="footer__widget">
                        <!--                        <h6>Shopping</h6>-->
                        <?= Nav::widget(['items' => $navItems, 'options' => ['class' => 'footer-nav']]) ?>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <?= SitePhonesWidget::widget(['containerOptions' => ['class' => 'footer-phone']]) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="footer__copyright__text">
                        <p>Copyright © <?= date('Y') ?> All rights reserved </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->
</div>


<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <?= Html::beginForm(['/site/search'], 'get', ['class' => 'search-model-form']) ?>
        <?= Html::input(
            'search',
            'q',
            '',
            [
                'id'          => "search-input",
                'placeholder' => Yii::t('site', 'Поиск...'),
            ]
        ) ?>
        <?php
        Html::endForm() ?>
        <!--        <form class="search-model-form">-->
        <!--            <input type="text" id="search-input" placeholder="Search here.....">-->
        <!--        </form>-->
    </div>
</div>
<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
    <script type="text/javascript">
        humane.log('<?php echo Html::encode($message);?>', {timeout: 2500});
    </script>
<?php
endforeach; ?>
<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>
