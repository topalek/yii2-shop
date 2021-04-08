<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\params\models\Params */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="params-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'sys_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?php
    if (!$model->required) echo $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Добавить' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
