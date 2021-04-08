<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Product */

$this->title = 'Редактировать: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'Товари', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_uk, 'url' => $model->seoUrl];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="catalog-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
