<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\components\BaseModel;
use common\components\BaseUrlManager;
use common\modules\translate\models\Translate;
use common\widgets\Alert;
use frontend\assets\AppAsset;
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
        'url'   => '/catalog',
    ],
    [
        'label' => Yii::t('site', 'О нас'),
        'url'   => '/o_nas',
    ],
    [
        'label' => Yii::t('site', 'Контакты'),
        'url'   => '/kontakty',
    ],
    [
        'label' => Yii::t('site', 'Доставка и оплата'),
        'url'   => '/dostavka_i_oplata',
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
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <?php
    $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
</head>
<body>
<?php
$this->beginBody() ?>
<div class="body-wrap">
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
    <?php
    if ($alert = Alert::widget()): ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?= $alert ?>
                </div>
            </div>
        </div>
    <?php
    endif; ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?= $content ?>
            </div>
        </div>
    </div>

    <?= $this->render('parts/footer', compact('navItems', 'langList', 'currentLang')) ?>
</div>


<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center jcc">
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
