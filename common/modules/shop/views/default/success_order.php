<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
$this->title = Yii::t('shop', 'Ваш заказ принят');
?>
<div class="order-success spad">
    <h2 class="text-center">
        <?= $this->title ?>
    </h2>
    <?= Html::img('/img/check.svg', ['class' => 'img-responsive success-img']) ?>
    <p>
        <b>
            <?= Yii::t('shop', 'Спасибо! Мы свяжемся с вами в ближайшее время') ?>
        </b>
    </p>
</div>
