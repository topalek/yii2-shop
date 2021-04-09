<?php

use backend\extensions\fileapi\FileAPIAdvanced;
use common\modules\catalog\models\Category;
use common\modules\image\widgets\InputWidget;
use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\money\MaskMoney;
use kartik\select2\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imperavi\Widget;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-item-form">

    <?php
    $form = ActiveForm::begin(
        [
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]
    ); ?>

    <?= SeoWidget::widget(['model' => $model]) ?>

    <dl class="tabs">
        <?php
        foreach (Translate::getLangList() as $lang => $langTitle) :?>
            <dt><?= $langTitle ?></dt>
            <dd>
                <?= $form->field($model, 'title_' . $lang)->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description_' . $lang)->widget(
                    Widget::class,
                    [
                        'options' => [
                            //                    'lang'        => 'ua',
                            'imageUpload'         => Url::to(
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

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'catalog_category_id')->widget(
                Select2::class,
                [
                    'data' => Category::getList(),
                ]
            ) ?>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'price_from')->widget(
                        MaskMoney::class,
                        [
                            'pluginOptions' => [
                                'prefix' => html_entity_decode('&#8372; '),
                            ],
                        ]
                    ) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'price_to')->widget(
                        MaskMoney::class,
                        [
                            'pluginOptions' => [
                                'prefix' => html_entity_decode('&#8372; '),
                            ],
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'main_img')->widget(
        FileAPIAdvanced::class,
        [
            'url'              => $model->modelUploadsUrl(),
            'deleteUrl'        => Url::toRoute('/catalog/product-admin/delete-image?id=' . $model->id),
            'deleteTempUrl'    => Url::toRoute('/catalog/product-admin/delete-temp-image'),
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

    <?= InputWidget::widget(
        [
            'model'                => $model,
            'attribute'            => 'originalImgFile',
            'initialPreviewMethod' => 'originalImgPreview',
            'options'              => [
                'multiple'         => false,
                'overwriteInitial' => true,
            ],
        ]
    ) ?>

    <?= InputWidget::widget(['model' => $model, 'attribute' => 'imgFiles']) ?>

    <?= $form->field($model, 'status')->widget(
        SwitchInput::class,
        [
            'pluginOptions' => [
                'size'    => 'small',
                'onText'  => 'Да',
                'offText' => 'Нет',
            ],
        ]
    )->label('Публиковать'); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

    <?php
    if (!$model->isNewRecord): ?>
        <div class="panel panel-default">
            <div class="panel-heading" onclick="$(this).next().toggle('fast');" style="cursor: pointer">
                <h3 class="panel-title">Модификации</h3>
            </div>
            <div class="panel-body item-properties">
                <p class="text-center">
                    <?= Html::a(
                        'Добавить язык',
                        ['/catalog/product-admin/add-property', 'item_id' => $model->id],
                        ['class' => 'add-new-property btn btn-primary']
                    ) ?>
                </p>

                <div class="property-list clearfix">
                    <?php
                    foreach ($model->properties as $property) {
                        echo $this->render('_item_property_view', ['model' => $property]);
                    } ?>
                </div>
            </div>
        </div>
    <?php
    endif; ?>
</div>

<?php
$this->registerJs(
    <<<JS
$(document).on('click','.add-new-property',function(e) {
  e.preventDefault();
  $.get(this.href,function(result) {
    $('.wrapper').before('<div id="prepend-block">'+result+'</div>');
  });
  return false;
});

$(document).on('click','.update-property',function(e) {
    e.preventDefault();
    var propertyBlock = $(this).parents('.item-property');
    $.post(this.href,function(result) {
     $('.wrapper').before('<div id="prepend-block">'+result+'</div>');
    })
});

$(document).on('click','.delete-property',function(e) {
    e.preventDefault();
    var link = $(this);
    if(confirm('Ви уверенны?'))
    {
        $.ajax({
            url: this.href,
            type: 'post',
            success: function(result) {
              $(link).parents('.item-property').remove();
            }
        });     
    }
});

// $(document).on('click','.cancel-form',function(e) {
//   $('.item-property[data-id='+$(this).data('id')+']').removeClass('disabled');
// })
JS
);
?>
