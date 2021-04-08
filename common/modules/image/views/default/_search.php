<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\image\models\ImageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="image-search">

    <?php
    $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'model_name') ?>

    <?= $form->field($model, 'model_id') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'is_main') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
