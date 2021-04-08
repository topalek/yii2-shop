<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\params\models\Params */

$this->title = 'Редактировать параметр: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Параметри', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="params-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
