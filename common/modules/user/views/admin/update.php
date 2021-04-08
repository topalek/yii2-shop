<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\user\models\User */

$this->title = 'Редактировать користувача: ' . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Користувачі', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
