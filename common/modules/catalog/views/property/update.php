<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Property */

$this->title = 'Редактировать опцію: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'Опції', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_uk];
$this->params['breadcrumbs'][] = 'Редактировать';
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
