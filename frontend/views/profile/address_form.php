<?php
/**
 * Created by topalek
 * Date: 19.01.2021
 * Time: 23:48
 */

use common\models\UserAddress;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $address UserAddress
 * @var $this    View
 */

?>


<?php
if (isset($success) && $success): ?>
    <?= Alert::widget(
        ['body' => 'Address was successfully updated', 'options' => ['class' => 'alert alert-success']]
    ) ?>
<?php
endif; ?>
<?php
$form = ActiveForm::begin(
    [
        'id'      => 'form-address',
        'options' => [
            'data-pjax' => 1,
        ],
        'action'  => 'update-address',
    ]
); ?>
<?= $form->field($address, 'address')->textInput() ?>
<?= $form->field($address, 'city')->textInput() ?>
<?= $form->field($address, 'state')->textInput() ?>
<?= $form->field($address, 'country')->textInput() ?>
<?= $form->field($address, 'zipcode')->textInput() ?>
<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'primary-btn']) ?>
</div>
<?php
ActiveForm::end();
?>
