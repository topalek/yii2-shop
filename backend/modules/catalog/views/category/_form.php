<?php

use backend\extensions\fileapi\FileAPIAdvanced;
use common\modules\catalog\models\Category;
use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-category-form">

    <?php
    $form = ActiveForm::begin(
        [
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]
    ); ?>

    <?= SeoWidget::widget(['model' => $model]) ?>

    <?= $form->field($model, 'parentId')->widget(
        Select2::class,
        [
            'data'          => Category::roots(),
            'options'       => ['placeholder' => 'Первый уровень'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]
    ) ?>

    <dl class="tabs">
        <?php
        foreach (Translate::getLangList() as $lang => $langTitle) :?>
            <dt><?= $langTitle ?></dt>
            <dd>
                <?= $form->field($model, 'title_' . $lang)->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description_' . $lang)->textarea(['rows' => 6]) ?>
            </dd>
        <?php
        endforeach; ?>
    </dl>

    <?= $form->field($model, 'main_img')->widget(
        FileAPIAdvanced::class,
        [
            'url'              => $model->modelUploadsUrl(),
            'deleteUrl'        => Url::toRoute('/catalog/category/delete-image?id=' . $model->id),
            'deleteTempUrl'    => Url::toRoute('/catalog/category/delete-temp-image'),
            'crop'             => true,
            'cropResizeWidth'  => 300,
            'cropResizeHeight' => 400,
            'previewWidth'     => 300,
            'previewHeight'    => 400,
            'settings'         => [
                'url'       => Url::toRoute('uploadTempImage'),
                'imageSize' => [
                    'minWidth'  => 300,
                    'minHeight' => 400,
                ],
                'preview'   => [
                    'el'     => '.uploader-preview',
                    'width'  => 300,
                    'height' => 400,
                ],
            ],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
