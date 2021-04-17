<?php

/**
 * Created by topalek
 * Date: 12.04.2021
 * Time: 13:23
 */

/* @var $this \yii\web\View */

use yii\widgets\ListView;

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
                                       aria-expanded="false">Categories</a>
                                </div>
                                <div id="collapseOne" class="collapse" data-parent="#accordionExample" style="">
                                    <div class="card-body">
                                        <div class="shop__sidebar__categories">
                                            <ul class="nice-scroll" tabindex="1"
                                                style="overflow-y: hidden; outline: none;">
                                                <li><a href="#">Men (20)</a></li>
                                                <li><a href="#">Women (20)</a></li>
                                                <li><a href="#">Bags (20)</a></li>
                                                <li><a href="#">Clothing (20)</a></li>
                                                <li><a href="#">Shoes (20)</a></li>
                                                <li><a href="#">Accessories (20)</a></li>
                                                <li><a href="#">Kids (20)</a></li>
                                                <li><a href="#">Kids (20)</a></li>
                                                <li><a href="#">Kids (20)</a></li>
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
                    <?= ListView::widget(
                        [
                            'dataProvider' => $dataProvider,
                            'itemOptions'  => ['class' => 'col-lg-3 col-md-6 col-sm-6 product-item'],
                            'itemView'     => '@common/modules/shop/views/default/_product_item',
                        ]
                    ) ?>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->
