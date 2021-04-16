<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Property */

$this->title = 'Редактировать характеристику: ' . ' ' . $model->title_ru;
$this->params['breadcrumbs'][] = ['label' => 'Характеристики', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-property-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
