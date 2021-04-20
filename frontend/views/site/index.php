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
<div class="categories">
    <?php
    foreach ($categories as $category) :?>
        <div class="category__item">
            <div class="category__item__pic">
                <?= Html::img(dynamicImageUrl($category->getMainImgUrl(), 400, 400, 1),) ?>
            </div>
            <div class="category__item__text">
                <h2><?= $category->getMlTitle() ?></h2>
                <?= Html::a(Yii::t('shop', 'За покупками'), [$category->getSeoUrl()]) ?>
            </div>
        </div>
    <?php
    endforeach; ?>
</div>
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
