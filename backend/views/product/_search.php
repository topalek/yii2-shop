<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-item-search">

    <?php
    $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title_uk') ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'title_en') ?>

    <?= $form->field($model, 'description_uk') ?>

    <?php
    // echo $form->field($model, 'description_ru') ?>

    <?php
    // echo $form->field($model, 'description_en') ?>

    <?php
    // echo $form->field($model, 'price') ?>

    <?php
    // echo $form->field($model, 'main_img') ?>

    <?php
    // echo $form->field($model, 'catalog_category_id') ?>

    <?php
    // echo $form->field($model, 'status') ?>

    <?php
    // echo $form->field($model, 'updated_at') ?>

    <?php
    // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
