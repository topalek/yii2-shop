<?php

use common\modules\catalog\models\Category;
use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\money\MaskMoney;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imperavi\Widget;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

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
        <div class="col-sm-4">
            <?= $form->field($model, 'category_id')->widget(
                Select2::class,
                [
                    'data' => Category::getList(),
                ]
            ) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'stock')->input('number') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'price')->widget(
                MaskMoney::class,
                [
                    'pluginOptions' => [
                        'prefix' => html_entity_decode('&#8372; '),
                    ],
                ]
            ) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'order_count')->input('number') ?>
        </div>
    </div>
    <?= $form->field($model, 'originalImgFile')->widget(
        FileInput::class,
        [
            'pluginOptions' => [
                'showCaption'          => false,
                'showRemove'           => false,
                'showUpload'           => false,
                'browseClass'          => 'btn btn-primary btn-block',
                'browseIcon'           => '<i class="glyphicon glyphicon-camera"></i> ',
                'deleteUrl'            => Url::toRoute(
                    ['product/delete-img', 'id' => $model->id, 'model_name' => $model::getModelName(0)]
                ),
                'previewFileType'      => 'any',
                'initialPreview'       => $model->getMainImgUrl(),
                'initialPreviewAsData' => true,
            ],
        ]
    ) ?>
    <?= $form->field($model, 'imgFiles[]')->widget(
        FileInput::class,
        [
            'options'       => [
                'accept'   => 'image/*',
                'multiple' => true,
                'language' => 'ru',
            ],
            'pluginOptions' => [
                'showCaption'          => false,
                'showRemove'           => false,
                'showUpload'           => false,
                'browseClass'          => 'btn btn-primary btn-block',
                'browseIcon'           => '<i class="glyphicon glyphicon-camera"></i> ',
                'deleteUrl'            => Url::toRoute(['product/delete-additional-img', 'id' => $model->id]),
                'previewFileType'      => 'any',
                'initialPreviewConfig' => $model->initialPreviewConfig(),
                'initialPreview'       => $model->getAdditionalImgsUrl(),
                'initialPreviewAsData' => true,
            ],
            'pluginEvents'  => [
                'filesorted' => !$model->isNewRecord ? 'function(event, params) {
                    $.post("' . Url::to(["/product/sort-gallery"]) . '",{ sort:params, modelId:' . $model->id . '},(resp)=>console.log(resp))
                }' : '',
            ],
        ]
    ) ?>


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

    <!--    <?php
    /*    if (!$model->isNewRecord): */ ?>
        <div class="panel panel-default">
            <div class="panel-heading" onclick="$(this).next().toggle('fast');" style="cursor: pointer">
                <h3 class="panel-title">Модификации</h3>
            </div>
            <div class="panel-body item-properties">
                <p class="text-center">
                    <?
    /*= Html::a(
                            'Добавить',
                            ['/product/add-property', 'item_id' => $model->id],
                            ['class' => 'add-new-property btn btn-primary']
                        ) */ ?>
                </p>

                <div class="property-list clearfix">
                    <?php
    /*                    foreach ($model->properties as $property) {
                            echo $this->render('_item_property_view', ['model' => $property]);
                        } */ ?>
                </div>
            </div>
        </div>
    --><?php
    /*    endif; */ ?>
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
