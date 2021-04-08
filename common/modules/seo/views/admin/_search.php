<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                        $this
 * @var common\modules\seo\models\SeoSearch $model
 * @var yii\widgets\ActiveForm              $form
 */
?>

<div class="seo-search">

    <?php
    $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'keywords') ?>

    <?= $form->field($model, 'head_block') ?>

    <?php
    // echo $form->field($model, 'external_link') ?>

    <?php
    // echo $form->field($model, 'internal_link') ?>

    <?php
    // echo $form->field($model, 'external_link_with_cat') ?>

    <?php
    // echo $form->field($model, 'noindex') ?>

    <?php
    // echo $form->field($model, 'nofollow') ?>

    <?php
    // echo $form->field($model, 'in_sitemap') ?>

    <?php
    // echo $form->field($model, 'is_canonical') ?>

    <?php
    // echo $form->field($model, 'model_name') ?>

    <?php
    // echo $form->field($model, 'model_id') ?>

    <?php
    // echo $form->field($model, 'status') ?>

    <?php
    // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
