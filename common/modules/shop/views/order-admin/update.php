<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\shop\models\Order */

$this->title = 'Update Shop Order: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shop Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
