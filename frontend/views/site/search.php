<?php

/**
 * Created by topalek
 * Date: 21.04.2021
 * Time: 11:46
 */

/* @var $this \yii\web\View */
/* @var $q string */

/* @var $provider \yii\data\ActiveDataProvider */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = Yii::t('shop', 'Поиск: ' . $q);
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Shop Section Begin -->
<section class="search">
    <div class="row">
        <div class="col-md-12">
            <h2 class="search-title"><?= Html::decode($this->title) ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="shop__sidebar">
            </div>
        </div>
        <div class="col-lg-9">
            <?php
            echo ListView::widget(
                [
                    'dataProvider' => $provider,
                    'itemView'     => '@common/modules/shop/views/default/_product_item',
                    'options'      => [
                        'class' => "product-list",
                    ],
                    'pager'        => [
                        'options' => [
                            'class' => 'product__pagination',
                        ],
                    ],
                ]
            ); ?>

        </div>
    </div>
</section>
<!-- Shop Section End -->
