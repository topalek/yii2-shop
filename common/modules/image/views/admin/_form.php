<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\image\models\Image */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="image-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'model_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'model_id')->textInput() ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'is_main')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Добавить' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
