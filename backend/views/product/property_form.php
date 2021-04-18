<?php
/**
 *
 * @var $form    \yii\widgets\ActiveForm
 * @var $model   \common\modules\catalog\models\ProductProperty
 * @var $product \common\modules\catalog\models\Product
 * @var $this    \yii\web\View
 */

use common\modules\catalog\models\Property;
use common\modules\catalog\models\PropertyCategory;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php
$form = ActiveForm::begin(
    [
        'id'     => 'product-property-form',
        'action' => ($model->isNewRecord) ? Url::toRoute(
            ['/product/add-property', 'product_id' => $model->product_id]
        ) :
            Url::toRoute(['/product/update-property', 'id' => $model->id]),
    ]
) ?>
<div class="modal-header">
    <h4 class="modal-title">Добавить новую модификацию: <?= $product->title_ru ?></h4>
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
ActiveForm::end() ?>

