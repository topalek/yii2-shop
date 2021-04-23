<?php

/**
 * Created by topalek
 * Date: 12.04.2021
 * Time: 13:23
 */

/* @var $this \yii\web\View */
/* @var $categories \common\modules\catalog\models\Category[] */
/* @var $productCount integer */

/* @var $dataProvider ActiveDataProvider */


use frontend\widgets\SideNavMenu;
use yii\data\ActiveDataProvider;
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
                    <?= SideNavMenu::widget() ?>
                </div>
            </div>
            <div class="col-lg-9">
                <?= ListView::widget(
                    [
                        'dataProvider' => $dataProvider,
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
                ) ?>

            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->
