<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\PropertySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-property-search">

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

    <?= $form->field($model, 'property_category_id') ?>

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
