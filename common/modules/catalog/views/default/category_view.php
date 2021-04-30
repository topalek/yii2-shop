<?php

/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 07.03.16
 * Time: 12:32
 *
 * @var $model        \common\modules\catalog\models\Category
 * @var $this         \yii\web\View
 * @var $dataProvider ActiveDataProvider
 */

use frontend\widgets\Filters;
use frontend\widgets\SideNavMenu;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

$this->title = $model->getMlTitle();
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Каталог'), 'url' => ['/catalog/default/index']];
if ($model->parent) {
    $this->params['breadcrumbs'][] = ['label' => $model->parent->getMlTitle(), 'url' => $model->parent->getSeoUrl()];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-category-view">
    <div class="about-category">
        <?php
        if ($model->getMainImg()): ?>
            <div class="category-img">
                <?= $model->getMainImg() ?>
            </div>
        <?php
        endif; ?>
        <div class="category-text">
            <h1 class="page-title"><?= $this->title ?></h1>
            <p class="category-description"><?= $model->getMlContent() ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="shop__sidebar">
                <?= SideNavMenu::widget() ?>
                <?= Filters::widget() ?>
            </div>
        </div>
        <div class="col-lg-9">
            <?= ListView::widget(
                [
                    'dataProvider' => $dataProvider,
                    'itemView'     => '@common/modules/shop/views/default/_product_item',
                    'layout'       => "{summary}\n<div class='sort'>" . Yii::t(
                            'shop',
                            'Сортировка:'
                        ) . "{sorter}</div>\n{items}\n{pager}",
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




