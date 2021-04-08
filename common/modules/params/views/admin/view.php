<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\params\models\Params */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="params-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Создать новый', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            'Удалить',
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method'  => 'post',
                ],
            ]
        ) ?>
    </p>

    <?= DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'name',
                'sys_name',
                'value:ntext',
            ],
        ]
    ) ?>

</div>
