<?php

/**
 * @var $this           yii\web\View
 * @var $products       Product[]
 * @var $categories     Category[]
 */

use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use yii\helpers\Html;

$this->title = Yii::$app->name;
?>
<!-- Banner Section Begin -->
<section class="banner spad">
    <div class="container">
        <div class="categories">
            <?php
            foreach ($categories as $category) :?>
                <div class="banner__item">
                    <div class="banner__item__pic">
                        <?= Html::img(dynamicImageUrl($category->getMainImgUrl(), 400, 400, 1),) ?>
                    </div>
                    <div class="banner__item__text">
                        <h2><?= $category->getMlTitle() ?></h2>
                        <?= Html::a(Yii::t('shop', 'За покупками'), [$category->getSeoUrl()]) ?>
                    </div>
                </div>
            <?php
            endforeach; ?>
        </div>
        <div class="row">
            <div class="col-lg-7 offset-lg-4">

            </div>
            <div class="col-lg-5">
                <div class="banner__item banner__item--middle">
                    <div class="banner__item__pic">
                        <img src="/img/category/category-2.jpg" alt="">
                    </div>
                    <div class="banner__item__text">
                        <h2>Accessories</h2>
                        <a href="#">Shop now</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="banner__item banner__item--last">
                    <div class="banner__item__pic">
                        <img src="/img/category/category-3.jpg" alt="">
                    </div>
                    <div class="banner__item__text">
                        <h2>Shoes Spring 2030</h2>
                        <a href="#">Shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<!-- Product Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <?php
            if ($products): ?>
            <div class="col-lg-12">
                <h2 class="text-center">Best Sellers</h2>
            </div>
        </div>
        <div class="row">
            <?php
            foreach ($products as $product) :?>
                <div class="col-lg-3 col-md-6 col-sm-6 product-item">
                    <?= $this->render('@common/modules/shop/views/default/_product_item', ['model' => $product]) ?>
                </div>
            <?php
            endforeach; ?>

            <?php
            else: ?>
            <?php
            endif; ?>
        </div>
    </div>
</section>
<!-- Product Section End -->
