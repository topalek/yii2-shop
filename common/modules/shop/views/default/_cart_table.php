<?php
/**
 * Created by topalek
 * Date: 20.04.2021
 * Time: 14:09
 *
 * @var $cartItems array
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$cartIsEmpty = (count($cartItems) < 1);
$totalSum = 0;
?>

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
        ?>
        <tr class="cart__product__item">
            <td class="product__cart__item">
                <div class="product__cart__item__pic">
                    <?= Html::img(
                        dynamicImageUrl($cartItem['photo'], 100),
                        [
                            'class' => 'img-responsive',
                        ]
                    ) ?>
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
                                'data-id'    => $cartItem['id'],
                                'data-price' => $cartItem['price'],
                                'data-url'   => Url::toRoute(['/shop/default/change-qty']),
                                'class'      => 'price-input',
                            ]
                        ) ?>
                        <i class="fa fa-plus plus-btn"></i>
                    </div>
                </div>
            </td>
            <td class="cart__price"><span><?= $cartItem['price'] * $cartItem['qty'] ?></span> грн.</td>
            <td class="cart__close">
                <?= Html::a(
                    '<i class="fa fa-close"></i>',
                    ['/shop/default/delete-cart-item', 'id' => $cartItem['id']],
                    [
                        'class' => 'delete-item-btn',
                    ]
                ) ?>
            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
</table>
