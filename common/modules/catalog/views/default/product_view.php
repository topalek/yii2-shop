<?php

use yii\helpers\Html;

/**
 * @var $this            yii\web\View
 * @var $model           common\modules\catalog\models\Product
 * @var $property        \common\modules\catalog\models\ProductProperty
 * @var $defaultProperty \common\modules\catalog\models\ProductProperty
 * @var $properties      \common\modules\catalog\models\ProductProperty[]
 */

$this->title = Html::decode($model->getMlTitle());
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Каталог'), 'url' => ['/catalog/default/index']];

if ($model->category->parent) {
    $this->params['breadcrumbs'][] = [
        'label' => $model->category->parent->getMlTitle(),
        'url'   => $model->category->parent->getSeoUrl(),
    ];
}
$this->params['breadcrumbs'][] = ['label' => $model->category->getMlTitle(), 'url' => $model->category->getSeoUrl()];
$this->params['breadcrumbs'][] = $this->title;

$images = [];

$propertyData = [];

$this->registerCssFile(Yii::$app->request->baseUrl . '/js/fancybox/jquery.fancybox.css');
$this->registerJsFile(
    Yii::$app->request->baseUrl . '/js/fancybox/jquery.fancybox.js',
    ['depends' => 'yii\web\JqueryAsset']
);
$this->registerJsFile(
    Yii::$app->request->baseUrl . '/js/jquery.elevateZoom-3.0.8.min.js',
    ['depends' => 'yii\web\JqueryAsset']
);

?>
    <div class="catalog-product-view">

        <div class="row">
            <div class="col-sm-4">
                <div class="main-img-block" data-thumb="<?= $model->getMainImgUrl() ?>"
                     data-full-img="<?= $model->getMainImgUrl() ?>">
                    <?= Html::a(
                        Html::img(
                            $model->getMainImgUrl(),
                            [
                                'class'           => 'img-responsive main-image',
                                'data-zoom-image' => $model->getMainImgUrl(),
                                'alt'             => $model->getMlTitle(),
                            ]
                        ),
                        $model->getMainImgUrl(),
                        ['class' => 'fancy-box', 'rel' => 'cat']
                    ); ?>
                </div>
                <?php
                if ($model->additional_images): ?>
                    <div class="row">
                        <?php
                        foreach ($model->additional_images as $image) {
                            echo Html::a(
                                Html::img($image, ['class' => 'img-responsive', 'alt' => $model->getMlTitle()]),
                                $image,
                                ['class' => 'fancy-box col-sm-3 col-xs-6 col-xxs-12', 'rel' => 'cat']
                            );
                        } ?>
                    </div>
                <?php
                endif; ?>
            </div>
            <div class="col-sm-8">
                <div class="about">
                    <h1 class="product-title"><?= Html::encode($this->title) ?></h1>
                    <div class="product-info">
                        <div class="props">
                            <?php
                            if ($properties): ?>
                                <ul class="property-list">
                                    <?php

                                    foreach ($properties as $key => $property) {
                                        echo Html::beginTag('li');
                                        $propCat = $property->propertyCategory->getMlTitle(
                                        ) ? $property->propertyCategory->getMlTitle(
                                        ) : $property->propertyCategory->getMlTitle('ru');
                                        var_dump($property->property->getMlTitle('uk'));
                                        $propTitle = $property->property->getMlTitle(
                                        ) ? $property->property->getMlTitle() : $property->property->getMlTitle('ru');
                                        echo Html::tag('strong', $propCat . ':');
                                        echo Html::tag(
                                            'span',
                                            $propTitle,
                                            [
                                                'class' => ($property->id == $defaultProperty->id) ? 'active' : '',
                                            ]
                                        );
                                        echo Html::endTag('li');
                                    } ?>
                                </ul>
                            <?php
                            endif; ?>
                        </div>

                        <div class="product-actions">
                            <div class="article">
                                <?= Yii::t('shop', 'Код товара:') ?> <strong><?= $model->article ?></strong>
                            </div>
                            <div class="price" data-price="<?= $model->price ?>">
                                <span><?= asMoney($model->price) ?></span> грн.
                            </div>
                            <?= Html::a(
                                Yii::t('shop', 'Купить'),
                                ['/shop/default/add-to-cart', 'id' => $model->id],
                                ['class' => 'add-to-cart primary-btn']
                            ) ?>
                        </div>
                    </div>
                    <div class="description">
                        <?= $model->getMlContent() ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php
$this->registerJs(
    <<<JS
$('.fancy-box').fancybox({
    padding : 0,
    maxWidth: 1200
});

function initZoomer() {
  $('.zoomContainer').remove();
  $('.main-image').elevateZoom({ zoomType	: "lens", lensShape : "round", lensSize : 200 });
}

initZoomer();

$('.property-list span').click(function(e) {
    if($(this).hasClass('active')){
        $(this).removeClass('active');
        $('.price span').html($('.price').data('price'));
        var mainImgBlock = $('.main-img-block');
        if($(mainImgBlock).hasClass('changed'))
        {
            $(mainImgBlock).removeClass('changed');
            $('img',mainImgBlock).data('zoomImage',$(mainImgBlock).data('fullImg')).attr('src',$(mainImgBlock).data('thumb'));
            initZoomer();
        }
    }
    else {
        $(this).parents('.property-list').find('span.active').removeClass('active');  
        $(this).addClass('active');
        $('.price span').html($(this).data('price'));
        var thumbImg = $(this).data('thumbImg');
        if(thumbImg)
        {
            $('.main-img-block img').data('zoomImage',$(this).data('fullImg')).attr('src',thumbImg).parent().addClass('changed');
            initZoomer();
        }
    }
});
JS
);
