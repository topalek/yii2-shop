<?php
/**
 * Created by Yatskanych Oleksandr
 *
 * @var $model \common\modules\shop\models\Order
 * @var $form  ActiveForm
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$totalSum = 0;
?>
<div class="order-page">
    <div class="row">
        <div class="col-sm-12 empty-text-block hide text-center">
            <h3><?= Yii::t('shop', 'Ваша корзина пуста') ?></h3>
        </div>
        <div class="col-sm-6 form-block">

            <h3><?= Yii::t('shop', 'Оформление заказа') ?></h3>

            <div class="order-form">
                <?php
                $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput() ?>

                <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

                <?= $form->field($model, 'phone')->textInput(['type' => 'phone']) ?>

                <?= $form->field($model, 'delivery_info')->textarea(
                    ['placeholder' => Yii::t('shop', 'Город, отделение Новой Почты')]
                ) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('shop', 'Отправить'), ['class' => 'btn btn-default']) ?>
                </div>

                <?php
                ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-sm-5 col-sm-offset-1 items-block">
            <h3><?= Yii::t('shop', 'Ваш заказ') ?></h3>
            <div class="items cart-items-list"
                 data-url="<?= Url::to(['/shop/default/order', 'updateContainer' => true]) ?>">
                <?php
                foreach ($model->cartItems as $cartItem): ?>
                    <?php
                    $totalSum += $cartItem['price'] * $cartItem['qty'];
                    $title = $cartItem['title_' . Yii::$app->language];
                    $modification = ArrayHelper::getValue($cartItem, 'charTitle_' . Yii::$app->language);
                    if ($modification) {
                        $title .= '<br>(' . $modification . ')';
                    }
                    ?>
                    <div class="item">
                        <div>
                            <div class="photo">
                                <?= $cartItem['photo'] ?>
                            </div>
                        </div>
                        <div>
                            <div class="title">
                                <?= Html::a($title, [$cartItem['url']]) ?>
                            </div>
                            <div class="price">
                                <span><?= $cartItem['price'] ?></span> грн.
                            </div>
                            <div class="counter">
                                <i class="fa fa-minus minus-btn"></i>
                                <?= Html::textInput(
                                    "qty",
                                    $cartItem['qty'],
                                    [
                                        'data-id'      => $cartItem['id'],
                                        'data-url'     => Url::toRoute(['/shop/default/change-qty']),
                                        'data-char-id' => ArrayHelper::getValue($cartItem, 'char_id'),
                                    ]
                                ) ?>
                                <i class="fa fa-plus plus-btn"></i>
                            </div>
                            <?= Html::a(
                                Yii::t('site', 'Удалить'),
                                ['/shop/default/delete-cart-item', 'id' => $cartItem['id']],
                                [
                                    'class'        => 'delete-item-btn',
                                    'data-char-id' => ArrayHelper::getValue($cartItem, 'char_id'),
                                ]
                            ) ?>
                        </div>
                    </div>
                <?php
                endforeach; ?>
                <div class="item total text-right">
                    <?= Yii::t('shop', 'Итого:') ?> <span><?= $totalSum ?></span> грн.
                </div>
            </div>
        </div>
    </div>
</div>



