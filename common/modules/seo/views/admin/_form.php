<?php

use common\modules\translate\models\Translate;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                  $this
 * @var common\modules\seo\models\Seo $model
 * @var yii\widgets\ActiveForm        $form
 */
?>

<div class="seo-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <dl class="tabs">
        <?php
        foreach (Translate::getLangList() as $lang => $langTitle) :?>
            <dt><?= $langTitle ?></dt>
            <dd>
                <?= $form->field($model, 'title_' . $lang)->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'description_' . $lang)->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'keywords_' . $lang)->textInput(['maxlength' => 255]) ?>
            </dd>
        <?php
        endforeach; ?>

    </dl>

    <?= $form->field($model, 'external_link')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'internal_link')->textInput(['maxlength' => 255]) ?>

    <div class="panel panel-primary">
        <div class="panel-heading" onclick="$(this).next().toggle('fast');" style="cursor: pointer">
            <h3 class="panel-title">Привязать к модели</h3>
        </div>
        <div class="panel-body" style="display: none">
            <?= $form->field($model, 'model_id')->textInput() ?>
            <?= $form->field($model, 'model_name')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
