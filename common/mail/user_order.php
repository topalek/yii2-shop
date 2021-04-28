<?php
/**
 * Created by topalek.
 *
 * @var $model \common\modules\shop\models\Order
 */

use yii\helpers\Html;
use yii\helpers\Url;

$totalPrice = 0;
?>
<h3>
    <?= Yii::t('shop', 'Спасибо, что выбрали наш магазин') ?>
</h3>
<style>
    th, td {
        padding: 8px;
        border-top: 1px solid #EEEEEE;
        border-bottom: 1px solid #EEEEEE;
    }
</style>
<div>
    <strong> <?= Yii::t('shop', 'Ваш заказ:') ?></strong> <br>
    <table>
        <thead>
        <tr>
            <th>Название:</th>
            <th>Цена:</th>
            <th>Количество:</th>
        </tr>

        </thead>
        <tbody>
        <?php
        foreach ($model->products as $cartItem):
            $totalPrice += $cartItem['price'] * $cartItem['qty'];
            ?>
            <tr>
                <td>
                    <?= Html::a($cartItem['title_ru'], Url::to($cartItem['url'], true)) ?>
                </td>
                <td><?= $cartItem['price'] ?> грн.</td>
                <td>
                    <?= $cartItem['qty'] ?> шт.
                </td>
            </tr>
        <?php
        endforeach; ?>
        <tr>
            <td colspan="2">
                <?= Yii::t('shop', 'На общую сумму:') ?>
            </td>
            <td>
                <?= $totalPrice ?> грн.
            </td>
        </tr>
        </tbody>
    </table>

</div>


