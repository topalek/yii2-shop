<?php

use common\modules\catalog\models\Category;
use common\modules\seo\widgets\SeoWidget;
use common\modules\translate\models\Translate;
use kartik\money\MaskMoney;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use kartik\widgets\SwitchInput;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
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
                        'settings' => [
                            'lang'        => 'ru',
                            'minHeight'   => '350px',
                            'imageUpload' => Url::to(
                                [
                                    'imperavi-upload',
                                    'model_name' => $model->getModelName(),
                                    'model_id'   => $model->id,
                                ]
                            ),

                            'imageDeleteCallback' => new \yii\web\JsExpression(
                                'function (url, image) { 
                                 $.ajax({
                                    url: "delete-imperavi-img?model=product",
                                    type: "post",
                                    data: {imgUrl:$(image).attr("src"), _csrf: yii.getCsrfToken()}
                                });
                             }'
                            ),
                            'imageManagerJson'    => Url::to(
                                [
                                    'images-get',
                                    'model_name' => $model->getModelName(),
                                    'model_id'   => $model->id,
                                ]
                            ),
                            'plugins'             => [
                                'fullscreen',
                                'fontcolor',
                                'fontsize',
                                'imagemanager',
                            ],
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
                'onText'  => '????',
                'offText' => '??????',
            ],
        ]
    )->label('??????????????????????'); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? '??????????????' : '????????????????',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

    <?php
    if (!$model->isNewRecord): ?>
        <div class="panel panel-default">
            <div class="panel-heading" onclick="$(this).next().toggle('fast');" style="cursor: pointer">
                <h3 class="panel-title">????????????????????????????</h3>
            </div>
            <div class="panel-body item-properties">
                <p class="text-center">
                    <?= Html::a(
                        '????????????????',
                        ['/product/add-property', 'product_id' => $model->id],
                        ['class' => 'add-new-property btn btn-primary']
                    ) ?>
                </p>

                <div class="properties">
                    <?php
                    if ($model->properties): ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">??????????????????</th>
                                <th scope="col">????????????????????????????</th>
                                <th scope="col">????????????????</th>
                            </tr>
                            </thead>
                            <tbody class="property-list">
                            <?php
                            foreach ($model->properties as $property) :?>
                                <tr class="product-property" data-id="<?= $property->id ?>">
                                    <td><?= $property->category->getMLTitle() ?></td>
                                    <td><?= $property->getMLTitle() ?></td>
                                    <td class="actions">
                                        <?= Html::a(
                                            '<i class="fa fa-pencil"></i>',
                                            [
                                                '/product/update-property',
                                                'property_id' => $property->id,
                                                'product_id'  => $model->id,
                                            ],
                                            ['class' => 'update-property']
                                        ) ?>
                                        <?= Html::a(
                                            '<i class="fa fa-times"></i>',
                                            [
                                                '/product/delete-property',
                                                'property_id' => $property->id,
                                                'product_id'  => $model->id,
                                            ],
                                            ['class' => 'delete-property']
                                        ) ?>
                                    </td>
                                </tr>
                            <?php
                            endforeach; ?>
                            </tbody>
                        </table>
                    <?php
                    endif; ?>
                </div>
            </div>
        </div>
    <?php
    endif; ?>
</div>

<?php
$this->registerJs(
    <<<JS
/*$(document).on('click','.add-new-property',function(e) {
  e.preventDefault();
  $.get(this.href,function(result) {
    $('.wrapper').append('<div id="prepend-block">'+result+'</div>');
  });
  return false;
});

$(document).on('click','.update-property',function(e) {
    e.preventDefault();
    var propertyBlock = $(this).parents('.item-property');
    $.post(this.href,function(result) {
     $('.wrapper').append('<div id="prepend-block">'+result+'</div>');
    })
});*/

$(document).on('click','.delete-property',function(e) {
    e.preventDefault();
    var link = $(this);
    if(confirm('???? ?????????????????'))
    {
        $.ajax({
            url: this.href,
            type: 'post',
            success: function(result) {
              $(link).parents('.product-property').remove();
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
