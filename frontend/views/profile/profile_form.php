<?php
/**
 * Created by topalek
 * Date: 20.01.2021
 * Time: 20:00
 */

use common\models\User;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $user User
 * @var $this View
 */
?>

<?php
$form = ActiveForm::begin(['id' => 'form-signup']); ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($user, 'lastname')->textInput() ?>
    </div>
</div>
<?= $form->field($user, 'username')->textInput() ?>

<?= $form->field($user, 'email')->textInput() ?>

<?= Html::button(
    'Change password',
    ['onclick' => '$(this).next(".new-password").toggle()', 'class' => 'btn btn-link']
) ?>
<div class="new-password" style="display: none">
    <div class="row">
        <div class="col">
            <?= $form->field($user, 'password')->passwordInput() ?>
        </div>
        <div class="col">
            <?= $form->field($user, 'passwordConfirm')->passwordInput() ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'primary-btn']) ?>
</div>

<?php
ActiveForm::end(); ?>
