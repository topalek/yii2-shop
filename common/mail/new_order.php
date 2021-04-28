<?php
/**
 * Created by topalek.
 *
 * @var $model \common\modules\shop\models\Order
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$totalPrice = 0;
?>

<p>
    <?= Html::a('Посмотреть в админ панели', Url::toRoute(['/shop/order/view', 'id' => $model->id], true)) ?>
</p>

<p>
    <strong>Информация о заказчике</strong> <br> <br>
    <strong>Имя:</strong> <?= $model->name ?> <br>
    <strong>Email:</strong> <?= $model->email ?> <br>
    <strong>Телефон:</strong> <?= $model->phone ?> <br>
    <strong>Иноформация о доставке:</strong> <?= $model->delivery_info ?>
</p>

<div>
    <strong>Заказ</strong> <br>
    <?php
    foreach ($model->products as $cartItem): $totalPrice += $cartItem['price'] * $cartItem['qty']; ?>
        <div style="padding-bottom: 20px; border-bottom: 1px solid #e3e3e3;">
            <strong>Название:</strong> <?= Html::a($cartItem['title_uk'], Url::to($cartItem['url'], true)) ?> <br>
            <strong>Цена:</strong> <?= $cartItem['price'] ?>
            <strong>Количество:</strong> <?= $cartItem['qty'] ?>
            <?php
            $modification = ArrayHelper::getValue($cartItem, 'charTitle_ru');
            if ($modification) {
                echo Html::tag('strong', 'Модификация:') . ' ' . $modification;
            }
            ?>
        </div>
    <?php
    endforeach; ?>
</div>


