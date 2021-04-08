<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\params\models\Params */

$this->title = 'Добавить параметр';
$this->params['breadcrumbs'][] = ['label' => 'Параметры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="params-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
