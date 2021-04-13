<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\shop\models\Order */

$this->title = '№ ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$totalSum = 0;
?>
<div class="shop-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if ($model->status == $model::STATUS_NEW || $model->status == $model::STATUS_IN_PROCESS) {
            echo Html::a('Выполнен', ['mark-done', 'id' => $model->id], ['class' => 'btn btn-success']);
        }
        if ($model->status == $model::STATUS_NEW) {
            echo ' ' . Html::a('Выполняется', ['mark-in-process', 'id' => $model->id], ['class' => 'btn btn-info']);
        }
        ?>
    </p>

    <?= DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'name',
                'email:email',
                'phone',
                'delivery_info:ntext',
                [
                    'format'    => 'raw',
                    'attribute' => 'status',
                    'value'     => $model->getStatusLabel(),
                ],
                'created_at',
            ],
        ]
    ) ?>

    <div class="items">
        <h3>Товары</h3>
        <table class="table table-striped table-bordered detail-view">
            <thead>
            <tr style="font-weight: 600;">
                <td>Название</td>
                <td>Количество</td>
                <td>Цена</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($model->cartItems as $cartItem): $totalSum += $cartItem['price'] * $cartItem['qty']; ?>
                <tr>
                    <td>
                        <?= $cartItem['photo'] ?>
                        <?= Html::a(
                            $cartItem['title_uk'],
                            ['/product/update', 'id' => $cartItem['id']]
                        ) ?>
                        <?php
                        $modification = ArrayHelper::getValue($cartItem, 'charTitle_ru');
                        if ($modification) {
                            echo " ($modification)";
                        }
                        ?>
                    </td>
                    <td style="text-align: center;">
                        <?= $cartItem['qty'] ?>
                    </td>
                    <td style="text-align: center;">
                        <?= $cartItem['price'] ?> грн.
                    </td>
                </tr>
            <?php
            endforeach; ?>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">
                    Разом: <?= $totalSum ?> грн.
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    .items img {
        max-width: 100px;
        display: inline-block;
        margin-right: 15px;
    }
</style>
