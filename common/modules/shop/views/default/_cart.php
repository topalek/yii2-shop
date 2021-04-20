<?php
/**
 * Created by topalek
 *
 * @var $this      Cart
 * @var $cartItems []
 */

use common\modules\shop\models\Cart;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$cartIsEmpty = (count($cartItems) < 1);
$totalSum = 0;
Modal::begin(
    [
        'id'     => 'cart-modal',
        'header' => '<h3 class="text-center">' . Yii::t('site', 'Корзина') . '</h3>',
        'size'   => Modal::SIZE_LARGE,
    ]
);
foreach ($cartItems as $cartItem) {
    $totalSum += $cartItem['price'] * $cartItem['qty'];
}
?>
    <!-- Shopping Cart Section Begin -->
    <section class="shopping-cart">
        <?php
        if (!$cartIsEmpty): ?>

            <div class="shopping__cart__table">
                <?= $this->render('_cart_table', compact('cartItems')) ?>
            </div>

            <div class="products-total text-right">
                <?= Yii::t('shop', 'Итого:') ?> <span><?= $totalSum ?></span> грн.
            </div>
        <?php
        endif; ?>

        <h4 class="text-center empty-cart-text <?= (!$cartIsEmpty) ? 'hide' : '' ?>"><?= Yii::t(
                'shop',
                'Ваша корзина пуста'
            ) ?></h4>

        <div class="d-flex jcsa buttons <?= ($cartIsEmpty) ? 'hide' : '' ?>">
            <?= Html::submitButton(Yii::t('shop', 'Продолжить'), ['class' => 'primary-btn']) ?>
            <?= Html::a(
                Yii::t('shop', 'Оформить заказ'),
                ['/shop/default/order'],
                ['class' => 'primary-btn']
            ) ?>
        </div>
    </section>
    <!-- Shopping Cart Section End -->
<?php

Modal::end();


