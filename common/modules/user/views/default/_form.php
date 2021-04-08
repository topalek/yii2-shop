<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 45]) ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
