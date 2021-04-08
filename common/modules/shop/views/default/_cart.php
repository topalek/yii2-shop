<?php
/**
 * Created by Yatskanych Oleksandr.
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

<?php
if (!$cartIsEmpty): ?>

    <div class="items cart-items-list">
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
            <div class="item">
                <div class="photo">
                    <?= $cartItem['photo'] ?>
                </div>
                <div class="title">
                    <?= Html::a($title, [$cartItem['url']]) ?>
                </div>
                <div class="counter">
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
                <div class="price">
                    <span><?= $cartItem['price'] ?></span> грн.
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

        <?php
        endforeach; ?>

        <div class="item total text-right">
            <?= Yii::t('shop', 'Итого:') ?> <span><?= $totalSum ?></span> грн.
        </div>
    </div>

<?php
endif; ?>

    <h4 class="text-center empty-cart-text <?= (!$cartIsEmpty) ? 'hide' : '' ?>"><?= Yii::t(
            'shop',
            'Ваша корзина пуста'
        ) ?></h4>

    <div class="text-center buttons <?= ($cartIsEmpty) ? 'hide' : '' ?>">
        <?= Html::submitButton(Yii::t('site', 'Продолжить'), ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('site', 'Оформить заказ'), ['/shop/default/order'], ['class' => 'btn btn-default']) ?>
    </div>

<?php
Modal::end();


