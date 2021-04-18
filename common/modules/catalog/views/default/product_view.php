<?php

use yii\helpers\Html;

/**
 * @var $this            yii\web\View
 * @var $model           common\modules\catalog\models\Product
 * @var $property        \common\modules\catalog\models\ProductProperty
 * @var $defaultProperty \common\modules\catalog\models\ProductProperty
 * @var $properties      \common\modules\catalog\models\ProductProperty[]
 */

$this->title = $model->getMlTitle();
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
            <div class="col-sm-4 main-img-block" data-thumb="<?= $model->getMainImgUrl() ?>"
                 data-full-img="<?= $model->getMainImgUrl() ?>">
                <?= Html::img(
                    $model->getMainImgUrl(),
                    [
                        'alt'             => $model->getMlTitle(),
                        'class'           => 'img-responsive main-image',
                        'data-zoom-image' => $model->getMainImgUrl(),
                    ]
                ) ?>
            </div>
            <div class="col-sm-8 about">
                <h1><?= Html::encode($this->title) ?></h1>

                <div class="price" data-price="<?= $model->price ?>">
                    <span><?= asMoney($model->price) ?></span> грн.
                </div>

                <div class="description">
                    <?= $model->getMlContent() ?>
                </div>

                <?php
                if ($properties): ?>
                    <ul class="property-list">
                        <?php
                        $prevCatId = null;
                        $openNew = false;
                        foreach ($properties as $key => $property) {
                            if ($property->property_category_id != $prevCatId) {
                                echo Html::beginTag('li');
                                echo Html::tag('strong', $property->propertyCategory->getMlTitle()) . ':';
                            }


                            echo Html::tag(
                                'span',
                                $property->property->getMlTitle(),
                                [
                                    'class' => ($property->id == $defaultProperty->id) ? 'active' : '',
                                ]
                            );

                            if (!isset($properties[$key + 1]) || $properties[$key + 1]->property_category_id != $property->property_category_id) {
                                echo Html::endTag('li');
                            }

                            $prevCatId = $property->property_category_id;
                            $openNew = false;
                        } ?>
                    </ul>
                <?php
                endif; ?>
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
                <?= Html::a(
                    Yii::t('shop', 'Купить'),
                    ['/shop/default/add-to-cart'],
                    ['class' => 'add-to-cart-btn primary-btn', 'data-item-id' => $model->id]
                ) ?>

                <?= frontend\widgets\SocialShareWidget::widget(
                    [
                        'title'     => $model->getMlTitle(),
                        'desc'      => getShortText($model->getMlContent(), 250, true),
                        'imgUrl'    => $model->modelUploadsUrl() . $model->main_img,
                        'imgWidth'  => 600,
                        'imgHeight' => 800,
                    ]
                ) ?>
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
