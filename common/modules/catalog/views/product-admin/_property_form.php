<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 21.03.16
 * Time: 20:49
 *
 * @var $form  \yii\widgets\ActiveForm
 * @var $model \common\modules\catalog\models\ProductProperty
 * @var $this  \yii\web\View
 */

use backend\extensions\fileapi\FileAPIAdvanced;
use common\modules\catalog\models\Property;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div id="property-form-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            $form = \yii\widgets\ActiveForm::begin(
                [
                    'id'     => 'item-property-form',
                    'action' => ($model->isNewRecord) ? Url::toRoute(
                        ['/catalog/product-admin/add-property', 'item_id' => $model->catalog_item_id]
                    ) :
                        Url::toRoute(['/catalog/product-admin/update-property', 'id' => $model->id]),
                ]
            ) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Добавить нову модифікацію</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'property_category_id')->widget(
                            \kartik\widgets\Select2::class,
                            [

                                'data'    => \common\modules\catalog\models\PropertyCategory::getList(),
                                'options' => [
                                    'placeholder' => 'Категорія опцій',
                                    'id'          => 'property-category',
                                ],
                            ]
                        ) ?>

                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'catalog_property_id')->widget(
                            \kartik\widgets\DepDrop::class,
                            [
                                'type'          => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                'data'          => ($model->property_category_id) ? Property::getListByCategory(
                                    $model->property_category_id
                                ) : null,
                                'options'       => [
                                    'placeholder' => 'Опція',
                                ],
                                'pluginOptions' => [
                                    'depends' => ['property-category'],
                                    'url'     => Url::toRoute(['/catalog/product-admin/dep-property']),
                                    'params'  => ['cat-id'],
                                ],
                            ]
                        ) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'photo', ['template' => '{label}{input}'])->widget(
                            FileAPIAdvanced::class,
                            [
                                'url'           => $model->modelUploadsUrl(),
                                'deleteUrl'     => Url::toRoute(
                                    '/catalog/product-admin/delete-property-image?id=' . $model->id
                                ),
                                'deleteTempUrl' => Url::toRoute('/catalog/product-admin/delete-property-temp-image'),
                                'crop'          => false,
                                'previewWidth'  => 250,
                                'previewHeight' => 450,
                                'settings'      => [
                                    'url'       => Url::toRoute('uploadPropertyTempImage'),
                                    'imageSize' => [
                                        'minWidth'  => 600,
                                        'minHeight' => 800,
                                    ],
                                    'preview'   => [
                                        'el'     => '.uploader-preview',
                                        'width'  => 250,
                                        'height' => 450,
                                    ],
                                ],
                            ]
                        ) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'price')->widget(
                            \kartik\money\MaskMoney::class,
                            [
                                'pluginOptions' => [
                                    'prefix' => html_entity_decode('&#8372; '),
                                ],
                            ]
                        ) ?>

                        <?= $form->field($model, 'default')->checkbox() ?>
                    </div>
                </div>

                <?= Html::activeHiddenInput($model, 'catalog_item_id') ?>

            </div>
            <div class="modal-footer text-center">
                <?= Html::button('Відміна', ['class' => 'btn btn-default cancel-form']) ?>
                <?= Html::submitButton(
                    ($model->isNewRecord) ? 'Добавить' : 'Зберегти',
                    ['class' => 'btn btn-success']
                ) ?>
            </div>
            <?php
            \yii\widgets\ActiveForm::end() ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$isUpdate = $model->isNewRecord;
$this->registerJs(
    <<<JS
    var isUpdate = '$isUpdate';
    $('#property-form-modal').modal({
      keyboard: false,
      show: true,
      backdrop: 'static',
    });
    
    $(document).on('click','.cancel-form',function() {
      $('#property-form-modal').modal('hide');
    })
    
    $('#property-form-modal').on('hidden.bs.modal', function (e) {
        $('#prepend-block').remove();
    });
    
    $('#item-property-form').on('beforeSubmit',function(e) {
        var form = $(this);
        if($(form).data().yiiActiveForm.validated==true)
        {
            $.ajax({
                url: $(form).attr('action'),
                type: 'post',
                dataType: 'json',
                data: form.serialize(),
                success: function(result) {
                  if(result.status == true)
                  {
                    if(!isUpdate)
                    {
                        $('.item-property[data-id=$model->id]').fadeOut(function() {
                          $(this).after(result.data);
                        });
                    }
                    else
                    {
                        $('.property-list').append(result.data);
                    }
                    $('#property-form-modal').modal('hide');
                  }
                  else 
                    humane.log(result.message);
                }
            });  
        }
        return false;
    });
JS
);
?>
