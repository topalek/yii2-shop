<?php

use common\models\Order;
use yii\web\View;

/**
 * Created by topalek
 * Date: 29.01.2021
 * Time: 10:04
 */

/* @var $this View */
/* @var $orders Order[] */

?>

<div class="col">
    <div class="card">
        <h5 class="card-header">Orders info</h5>
        <div class="card-body">
            <?php
            foreach ($orders as $order) {
                echo $this->render('_order', ['order' => $order]);
            } ?>
        </div>
    </div>
</div>

