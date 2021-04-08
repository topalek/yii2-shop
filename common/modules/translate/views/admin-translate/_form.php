<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\translate\models\Translate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'language')->dropDownList(
        ['uk' => 'Українська', 'en' => 'English'],
        ['disabled' => ($model->isNewRecord) ? false : true]
    ) ?>

    <?= $form->field($model, 'translation')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
