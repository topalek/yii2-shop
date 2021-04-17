<?php
/**
 * Created by topalek
 *
 * @var $cartItems []
 */

use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$cartIsEmpty = (count($cartItems) < 1);
$totalSum = 0;
Modal::begin(
    [
        'id'     => 'cart-modal',
        'header' => '<h3 class="text-center">' . Yii::t('site', 'Корзина') . '</h3>',
        'size'   => Modal::SIZE_LARGE,
    ]
);

?>
    <!-- Shopping Cart Section Begin -->
    <section class="shopping-cart spad">
        <?php
        if (!$cartIsEmpty): ?>

            <div class="shopping__cart__table">
                <table>
                    <thead>
                    <tr>
                        <th><?= Yii::t('shop', 'Продукт') ?></th>
                        <th><?= Yii::t('shop', 'Количество') ?></th>
                        <th><?= Yii::t('shop', 'Всего') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($cartItems as $cartItem): ?>
                        <?php
                        $totalSum += $cartItem['price'] * $cartItem['qty'];
                        $title = $cartItem['title_' . Yii::$app->language];
                        $modification = ArrayHelper::getValue($cartItem, 'charTitle_' . Yii::$app->language);
                        if ($modification) {
                            $title .= '<br>(' . $modification . ')';
                        }
                        ?>
                        <tr>
                            <td class="product__cart__item">
                                <div class="product__cart__item__pic">
                                    <?= $cartItem['photo'] ?>
                                </div>
                                <div class="product__cart__item__text">
                                    <h6> <?= Html::a($title, [$cartItem['url']]) ?></h6>
                                    <h5><span><?= $cartItem['price'] ?></span> грн.</h5>
                                </div>
                            </td>
                            <td class="quantity__item">
                                <div class="quantity">
                                    <div class="pro-qty-2">
                                        <i class="fa fa-minus minus-btn"></i>
                                        <?= Html::textInput(
                                            "qty",
                                            $cartItem['qty'],
                                            [
                                                'data-id'      => $cartItem['id'],
                                                'data-char-id' => ArrayHelper::getValue($cartItem, 'char_id'),
                                                'data-url'     => Url::toRoute(['/shop/default/change-qty']),
                                            ]
                                        ) ?>
                                        <i class="fa fa-plus plus-btn"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="cart__price"><span><?= $cartItem['price'] ?></span> грн.</td>
                            <td class="cart__close">
                                <?= Html::a(
                                    '<i class="fa fa-close"></i>',
                                    ['/shop/default/delete-cart-item', 'id' => $cartItem['id']],
                                    [
                                        'class'        => 'delete-item-btn',
                                        'data-char-id' => ArrayHelper::getValue($cartItem, 'char_id'),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="item total text-right">
                <?= Yii::t('shop', 'Итого:') ?> <span><?= $totalSum ?></span> грн.
            </div>
        <?php
        endif; ?>

        <h4 class="text-center empty-cart-text <?= (!$cartIsEmpty) ? 'hide' : '' ?>"><?= Yii::t(
                'shop',
                'Ваша корзина пуста'
            ) ?></h4>

        <div class="text-center buttons <?= ($cartIsEmpty) ? 'hide' : '' ?>">
            <div class="continue__btn">
                <?= Html::submitButton(Yii::t('shop', 'Продолжить'), ['class' => 'btn btn-default']) ?>
            </div>
            <div class="continue__btn update__btn">
                <?= Html::a(
                    Yii::t('shop', 'Оформить заказ'),
                    ['/shop/default/order'],
                    ['class' => 'btn btn-default']
                ) ?>
            </div>


        </div>
    </section>
    <!-- Shopping Cart Section End -->
<?php

Modal::end();


