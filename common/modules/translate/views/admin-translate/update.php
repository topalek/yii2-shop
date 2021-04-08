<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\translate\models\Translate */

$this->title = 'Редактировать перевод';
$this->params['breadcrumbs'][] = ['label' => 'Перевод', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'language' => $model->language]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
