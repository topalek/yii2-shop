<?php

use common\modules\catalog\models\PropertyCategory;
use common\modules\translate\models\Translate;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Property */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-property-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <dl class="tabs">
        <?php
        foreach (Translate::getLangList() as $lang => $langTitle) :?>
            <dt><?= $langTitle ?></dt>
            <dd>
                <?= $form->field($model, 'title_' . $lang)->textInput(['maxlength' => true]) ?>
            </dd>
        <?php
        endforeach; ?>
    </dl>

    <?= $form->field($model, 'property_category_id')->widget(
        Select2::class,
        [
            'data'    => PropertyCategory::getList(),
            'options' => [
                'placeholder' => 'Выберите категорию...',
            ],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
        <?= !$model->isNewRecord ? Html::a('Создать новую', ['create'], ['class' => 'btn btn-success']) : '' ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
