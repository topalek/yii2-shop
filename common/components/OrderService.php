<?php
/**
 * Created by topalek
 * Date: 06.04.2021
 * Time: 16:54
 */

namespace common\components;


use app\modules\shop\models\ShopOrder;

class OrderService
{
    public static function getNewOrdersCount()
    {
        $count = 0;
        if ($orders = OrderService::getNewOrders()) {
            $count = count($orders);
        }
        return $count;
    }

    public static function getNewOrders()
    {
        $mindate = (new \DateTime())->format('Y-m-d 00:00:00');
        $maxdate = (new \DateTime())->modify('+1 day')->format('Y-m-d 00:00:00');
        return ShopOrder::find()->where(["between", "created_at", $mindate, $maxdate])->andWhere(
            ['status' => ShopOrder::STATUS_NEW]
        )->all();
    }

}
