<?php

use common\modules\catalog\models\Category;
use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\widgets\FileInput;
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

    <?= $form->field($model, 'parent_id')->widget(
        Select2::class,
        [
            'data'          => Category::getList(),
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
    <?= $form->field($model, 'imgFile')->widget(
        FileInput::class,
        [
            'pluginOptions' => [
                'showCaption'          => false,
                'showRemove'           => false,
                'showUpload'           => false,
                'browseClass'          => 'btn btn-primary btn-block',
                'browseIcon'           => '<i class="glyphicon glyphicon-camera"></i> ',
                'deleteUrl'            => Url::toRoute(
                    ['category/delete-img', 'id' => $model->id, 'model_name' => $model::getModelName(0)]
                ),
                'previewFileType'      => 'any',
                'initialPreview'       => $model->getMainImgUrl(),
                'initialPreviewAsData' => true,
                'uploadUrl'            => Url::to(['/category/upload-img']),
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
