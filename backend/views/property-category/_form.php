<?php

use common\modules\catalog\models\Category;
use common\modules\translate\models\Translate;
use kartik\select2\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\PropertyCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="property-category-form">

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

    <?= $form->field($model, 'catalogCategoryIds')->widget(
        Select2::class,
        [
            'data'    => Category::getList(),
            'options' => [
                'multiple'    => true,
                'placeholder' => 'Выберите категорию...',
            ],
        ]
    ) ?>
    <?= $form->field($model, 'in_filters')->widget(
        SwitchInput::class,
        [
            'pluginOptions' => [
                'size'    => 'small',
                'onText'  => 'Да',
                'offText' => 'Нет',
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
