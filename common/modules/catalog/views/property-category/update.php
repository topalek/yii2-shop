<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\PropertyCategory */

$this->title = 'Редактировать категорию: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'Категории характеристик', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_uk];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="property-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
