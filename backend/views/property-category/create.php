<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\PropertyCategory */

$this->title = 'Создать категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории характеристик', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
