<?php

use common\components\OrderService;
use common\modules\shop\models\Order;
use yii\helpers\Url;

?>

<div class="admin-default-index">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= OrderService::getNewOrdersCount() ?></h3>

                    <p>Новых заказов</p>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-circle-right"></i>
                </div>
                <a href="<?= Url::to(['/shop/order/index', 'OrderSearch[status]' => Order::STATUS_NEW]) ?>"
                   class="small-box-footer">Перейти <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                    <p>Заказы выполняются</p>
                </div>
                <div class="icon">
                    <!--                    <i class="ion ion-stats-bars"></i>-->
                    <i class="fa fa-cart-arrow-down"></i>
                </div>
                <a href="<?= Url::to(
                    ['/shop/order-admin', 'OrderSearch[status]' => Order::STATUS_IN_PROCESS]
                ) ?>" class="small-box-footer">Перейти <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
