<?php
/**
 * Created by topalek
 *
 * @var $model \common\modules\shop\models\Order
 * @var $form  ActiveForm
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('shop', 'Оформление заказа');
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => '/catalog'];
$this->params['breadcrumbs'][] = $this->title;
$totalSum = 0;
foreach ($model->products as $cartItem) {
    $totalSum += $cartItem['price'] * $cartItem['qty'];
}
?>
<div class="order-page">
    <div class="row">
        <div class="col-lg-8">
            <h3><?= Yii::t('shop', 'Оформление заказа') ?></h3>
            <div class="shopping__cart__table">
                <?= $this->render('_cart_table', ['cartItems' => $model->products]) ?>
            </div>
        </div>
        <div class="col-lg-4">
            <h3><?= Yii::t('shop', 'Ваш заказ') ?></h3>
            <div class="cart__info">
                <div class="order-form">
                    <?php
                    $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'name')->textInput() ?>

                    <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

                    <?= $form->field($model, 'phone')->textInput(['type' => 'phone']) ?>

                    <?= $form->field($model, 'delivery_info')->textarea(
                        ['placeholder' => Yii::t('shop', 'Город, отделение Новой Почты')]
                    ) ?>
                    <div class="cart__total">
                        <div class="total-text">
                            <span><?= Yii::t('shop', 'Итого:') ?></span>
                            <div class="products-total">
                                <span><?= $totalSum ?></span> грн.
                            </div>
                        </div>

                        <?= Html::submitButton(Yii::t('shop', 'Заказать'), ['class' => 'primary-btn']) ?>
                    </div>
                    <?php
                    ActiveForm::end(); ?>
                </div>
            </div>

        </div>
    </div>
</div>



