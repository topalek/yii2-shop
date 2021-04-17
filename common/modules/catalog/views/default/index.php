<?php

/**
 * Created by topalek
 * Date: 12.04.2021
 * Time: 13:23
 */

/* @var $this \yii\web\View */
/* @var $categories \common\modules\catalog\models\Category[] */
/* @var $productCount integer */
/* @var $products \common\modules\catalog\models\Product[] */


$this->title = Yii::t('shop', 'Каталог');
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="shop__sidebar">
                    <div class="shop__sidebar__accordion">
                        <div class="accordion" id="accordionExample">

                            <div class="card">
                                <div class="card-heading">
                                    <a data-toggle="collapse" data-target="#collapseOne" class="collapsed"
                                       aria-expanded="false"><?= Yii::t('shop', 'Категории') ?></a>
                                </div>
                                <div id="collapseOne" class="collapse" data-parent="#accordionExample" style="">
                                    <div class="card-body">
                                        <div class="shop__sidebar__categories">
                                            <ul class="nice-scroll" tabindex="1"
                                                style="overflow-y: hidden; outline: none;">
                                                <?php
                                                foreach ($categories as $category):?>
                                                    <li><a href="<?= $category->getSeoUrl(
                                                        ) ?>"><?= $category->getMlTitle() ?></a></li>
                                                <?php
                                                endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="product-list">
                    <?php
                    foreach ($products as $product) :?>
                        <?= $this->render('@common/modules/shop/views/default/_product_item', ['model' => $product]) ?>
                    <?php
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->
