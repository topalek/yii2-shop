<?php

/**
 * Created by topalek
 * Date: 13.04.2021
 * Time: 9:54
 */

/* @var $this \yii\web\View */

/* @var $model \common\modules\catalog\models\ProductProperty */

use yii\helpers\Html;

?>

<div class="item-property" data-id="<?= $model->id ?>">
    <div class="text-right">
        <?= Html::a(
            '<i class="fa fa-pencil"></i>',
            ['/product/update-property', 'id' => $model->id],
            ['class' => 'update-property']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-times"></i>',
            ['/product/delete-property', 'id' => $model->id],
            ['class' => 'delete-property']
        ) ?>
    </div>
    <?= $model->getImg(150, 150) ?>
    <strong>Категория</strong>: <?= $model->property->category->title_ru ?><br>
    <strong>Опция</strong>: <?= $model->property->title_ru ?><br>
    <strong>Цена</strong>: <?= $model->price ?>
</div>
