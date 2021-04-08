<?php

use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\page\models\Page */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="page-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= SeoWidget::widget(['model' => $model]); ?>

    <dl class="tabs">
        <?php
        foreach (Translate::getLangList() as $lang => $langTitle) :?>
            <dt><?= $langTitle ?></dt>
            <dd>
                <?= $form->field($model, 'title_' . $lang)->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'content_' . $lang)->widget(
                    \yii\imperavi\Widget::class,
                    [
                        'options' => [
                            'lang'                => 'ru',
                            'imageUpload'         => \yii\helpers\Url::to(
                                ['/admin/default/upload-imperavi', 'module' => $model->getModelName()]
                            ),
                            'minHeight'           => '350px',
                            'uploadImageFields'   => [
                                Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken(),
                            ],
                            'uploadFileFields'    => [
                                Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken(),
                            ],
                            'imageDeleteCallback' => "function(url,image){
                            $.ajax({
                                url: '/admin/default/delete-imperavi-img?modeule=category',
                                type: 'post',
                                data: {imgUrl:$(image).attr('src'), _csrf: yii.getCsrfToken()}
                            });
                        }",
                        ],
                        'plugins' => [
                            'fullscreen',
                            'clips',
                            'fontcolor',
                            'fontfamily',
                            'fontsize',
                        ],
                    ]
                ) ?>
            </dd>
        <?php
        endforeach; ?>
    </dl>

    <?= $form->field($model, 'status')->widget(
        SwitchInput::class,
        [
            'pluginOptions' => [
                'size'    => 'small',
                'onText'  => 'Да',
                'offText' => 'Нет',
            ],
        ]
    ); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
