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
            <?php
            if ($category->getMainImgUrl()): ?>
                <div class="category__item__pic">
                    <?= Html::img(dynamicImageUrl($category->getMainImgUrl(), 200, 200, 1),) ?>
                </div>
            <?php
            endif; ?>
            <div class="category__item__text">
                <h2>
                    <?= Html::a(Yii::t('shop', $category->getMlTitle()), [$category->getSeoUrl()]) ?>
                </h2>
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
        <div class="products-grid">
            <?php
            foreach ($products as $product) :?>
                <?= $this->render('@common/modules/shop/views/default/_product_item', ['model' => $product]) ?>
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
