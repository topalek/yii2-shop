<?php

use common\models\Order;

/**
 * Created by topalek
 * Date: 29.01.2021
 * Time: 10:22
 */

/**@var $order Order */
?>
<div class="card">
    <div class="card-header">
        Order #<?= $order->id ?> <span class="badge badge-pill badge-primary text-right"><?= $order->status ?></span>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($order->orderItems as $item) {
                if ($i < 10) {
                    $k = '0' . $i;
                }
                $totalPrice = Yii::$app->formatter->asCurrency($item->unit_price * $item->quantity);
                echo sprintf(
                    "<tr><td>%s. %s</td> <td>%s</td> <td>%s</td> <td>%s</td></tr>",
                    $k,
                    $item->product_name,
                    $item->unit_price,
                    $item->quantity,
                    $totalPrice
                );
                $i++;
            } ?>
            </tbody>

        </table>

    </div>
</div>

