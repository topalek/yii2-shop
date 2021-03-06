<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 22.03.16
 * Time: 19:32
 *
 * @var $model \common\modules\catalog\models\ProductProperty
 */

?>

<div class="item-property" data-id="<?= $model->id ?>">
    <div class="text-right">
        <?= \yii\helpers\Html::a(
            '<i class="fa fa-pencil"></i>',
            ['/product/update-property', 'id' => $model->id],
            ['class' => 'update-property']
        ) ?>
        <?= \yii\helpers\Html::a(
            '<i class="fa fa-times"></i>',
            ['/product/delete-property', 'id' => $model->id],
            ['class' => 'delete-property']
        ) ?>
    </div>
    <?= $model->getImg(150, 150) ?>
    <strong>Категория</strong>: <?= $model->property->category->title_uk ?><br>
    <strong>Опция</strong>: <?= $model->property->title_uk ?><br>
    <strong>Цена</strong>: <?= $model->price ?>
</div>
