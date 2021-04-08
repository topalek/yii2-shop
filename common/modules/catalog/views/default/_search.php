<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 23.03.16
 * Time: 22:09
 *
 * @var $this  yii\web\View
 * @var $model \common\modules\catalog\models\ProductSearch
 * @var $form  yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php
$form = ActiveForm::begin(
    [
        'action' => [$categoryModel->seoUrl],
        'method' => 'get',
        'options' => ['class' => 'filter-form'],
    ]
); ?>

<?php
foreach ($propertyCategories as $propertyCategory) {
    $propertiesList = $propertyCategory->propertiesList;
    if ($propertiesList) {
        echo $form->field(
            $model,
            'propertyIds[' . $propertyCategory->id . '][]',
            ['template' => '<div class="checkbox-list"><strong>' . $propertyCategory->mlTitle . '</strong>{input}</div>']
        )->checkboxList(
            $propertyCategory->propertiesList,
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    $row = Html::beginTag('div', ['class' => 'checkbox-item']);
                    $row .= Html::checkbox($name, $checked, ['value' => $value, 'id' => $name . $index]) . Html::label(
                            $label,
                            $name . $index
                        );
                    $row .= Html::endTag('div');
                    return $row;
                },
            ]
        );
    }
} ?>

<?= Html::submitButton(Yii::t('site', 'Пошук'), ['class' => 'btn btn-default']); ?>
<?php
ActiveForm::end(); ?>
