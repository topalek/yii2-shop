<?php

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddress;
use yii\bootstrap4\ActiveForm;
use yii\helpers\BaseArrayHelper;
use yii\helpers\Url;

/**
 * Created by topalek
 * Date: 27.01.2021
 * Time: 9:41
 */

/* @var $this \yii\web\View */
/* @var $order Order */
/* @var $orderAddress OrderAddress */
/* @var $cartItems array */

$itemsForPayPal = [];
foreach ($cartItems as $cartItem) {
    $itemsForPayPal[] = [
        'name'        => $cartItem['name'],
        'unit_amount' => $cartItem['price'],
        'quantity'    => $cartItem['quantity'],
        'description' => BaseArrayHelper::getValue($cartItem, 'description', ''),
    ];
}

$this->title = 'Checkout';
$this->params['breadcrumbs'][] = [
    'label' => 'Cart',//Yii::t('shop', 'Cart'),
    'url'   => Url::to(['cart/index']),
];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://www.paypal.com/sdk/js?client-id=Ad4M2Gj2RC-LRYM638N5CdEIB9hU70-6dVXY-cQR3Z9NS8TbdgGzKyLdTtJCnLbJXpcMVvLZqvsz78Qg">
</script>

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="checkout__form">
        <?php
        $form = ActiveForm::begin() ?>
        <div class="row">
            <div class="col-lg-7 col-md-6">
                <h6 class="coupon__code">
                    <span class="icon_tag_alt"></span> Have a coupon? <a href="#">Click
                        here</a> to enter your code
                </h6>
                <h6 class="checkout__title">Billing Details</h6>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="checkout__input">
                            <?= $form->field($order, 'firstname')->textInput() ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="checkout__input">
                            <?= $form->field($order, 'lastname')->textInput() ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="checkout__input">
                            <?= $form->field($order, 'email')->input('email') ?>
                        </div>
                    </div>
                </div>
                <div class="checkout__input">
                    <?= $form->field($orderAddress, 'address')->textInput() ?>
                </div>
                <div class="checkout__input">
                    <?= $form->field($orderAddress, 'city')->textInput() ?>
                </div>
                <div class="checkout__input">
                    <?= $form->field($orderAddress, 'state')->textInput() ?>
                </div>
                <div class="checkout__input">
                    <?= $form->field($orderAddress, 'country')->textInput() ?>
                </div>
                <div class="checkout__input">
                    <?= $form->field($orderAddress, 'zipcode')->textInput() ?>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="checkout__order">
                    <h4 class="order__title">Your order</h4>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        foreach ($cartItems as $cartItem) {
                            if ($i < 10) {
                                $k = '0' . $i;
                            }
                            $price = Yii::$app->formatter->asCurrency($cartItem['price'] * $cartItem['quantity']);
                            echo sprintf(
                                "<tr><td class='d-inline-block text-truncate'>%s. %s</td> <td>%s</td> <td>%s</td></tr>",
                                $k,
                                $cartItem['name'],
                                $cartItem['quantity'],
                                $price
                            );
                            $i++;
                        } ?>
                        </tbody>

                    </table>
                    <ul class="checkout__total__products">

                    </ul>
                    <ul class="checkout__total__all">
                        <!--                                <li>Subtotal <span>$750.99</span></li>-->
                        <li>Total <span><?= Yii::$app->formatter->asCurrency(CartItem::getTotalPrice()) ?></span></li>
                    </ul>
                    <div class="checkout__input__checkbox">
                        <label for="acc-or">
                            Create an account?
                            <input type="checkbox" id="acc-or">
                            <span class="checkmark"></span>
                        </label>
                    </div>

                    <!--                    <div id="paypal-button-container"></div>-->
                    <p class="text-right">
                        <button type="submit" class="btn btn-secondary">Place order</button>
                    </p>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end() ?>
    </div>
</section>
<!-- Checkout Section End -->
<script>

    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: <?= CartItem::getTotalPrice()?>,
                    },
                }]
            })
        },
        onApprove: function (data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                const data = $('form').serializeArray();
                data.push(
                    {name: "Order[transaction_id]", value: details.id},
                    {name: "Order[status]", value: details.status == "COMPLETED" ? 1 : 2}
                );
                $.ajax({
                    method: 'post',
                    url: '<?= Url::to(["/cart/create-order"])?>',
                    data: data,
                    success: function (resp) {
                        console.log(resp)
                        // This function shows a transaction success message to your buyer.
                        alert('Thanks for your order ' + details.payer.name.given_name);
                        window.location.href = '';
                    }
                })

            });
        }
    }).render('#paypal-button-container');
</script>

<?php
$this->registerJs(
    <<<JS

JS
) ?>

