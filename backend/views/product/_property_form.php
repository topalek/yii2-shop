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

use common\modules\catalog\models\Property;
use common\modules\catalog\models\PropertyCategory;
use kartik\depdrop\DepDrop;
use kartik\money\MaskMoney;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div id="property-form-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            $form = \yii\widgets\ActiveForm::begin(
                [
                    'id'     => 'product-property-form',
                    'action' => ($model->isNewRecord) ? Url::toRoute(
                        ['/product/add-property', 'item_id' => $model->product_id]
                    ) :
                        Url::toRoute(['/product/update-property', 'id' => $model->id]),
                ]
            ) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Добавить новую модификацию</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'property_category_id')->widget(
                            Select2::class,
                            [

                                'data'    => PropertyCategory::getList(),
                                'options' => [
                                    'placeholder' => 'Категория опций',
                                    'id'          => 'property-category',
                                ],
                            ]
                        ) ?>

                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'property_id')->widget(
                            DepDrop::class,
                            [
                                'type'          => DepDrop::TYPE_SELECT2,
                                'data'          => ($model->property_category_id) ? Property::getListByCategory(
                                    $model->property_category_id
                                ) : null,
                                'options'       => [
                                    'placeholder' => 'Опция',
                                ],
                                'pluginOptions' => [
                                    'depends' => ['property-category'],
                                    'url'     => Url::toRoute(['/product/dep-property']),
                                    'params'  => ['cat-id'],
                                ],
                            ]
                        ) ?>
                    </div>

                    <!--                    <div class="col-sm-6">-->
                    <!--                        --><?
                    //= $form->field($model, 'photo', ['template' => '{label}{input}'])->widget() ?>
                    <!--                    </div>-->

                    <div class="col-sm-6">
                        <?= $form->field($model, 'price')->widget(
                            MaskMoney::class,
                            [
                                'pluginOptions' => [
                                    'prefix' => html_entity_decode('&#8372; '),
                                ],
                            ]
                        ) ?>

                        <?= $form->field($model, 'default')->checkbox() ?>
                    </div>
                </div>

                <?= Html::activeHiddenInput($model, 'product_id') ?>

            </div>
            <div class="modal-footer text-center">
                <?= Html::button('Отмена', ['class' => 'btn btn-default cancel-form']) ?>
                <?= Html::submitButton(
                    ($model->isNewRecord) ? 'Добавить' : 'Сохранить',
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
    let isUpdate = '$isUpdate';
let propertyModal = $('#property-form-modal');
    propertyModal.modal({
      keyboard: false,
      show: true,
      backdrop: 'static'
    });
    
    $(document).on('click','.cancel-form',function() {
      $('#property-form-modal').modal('hide');
    })
    
    propertyModal.on('hidden.bs.modal', function (e) {
        $('#prepend-block').remove();
    });
    
    $('#product-property-form').on('beforeSubmit',function(e) {
        var form = $(this);
        if($(form).data().yiiActiveForm.validated == true)
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
                        $('.product-property[data-id=$model->id]').fadeOut(function() {
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
