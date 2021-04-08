<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View                              $this
 * @var common\modules\htmlBlock\models\HtmlBlock $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Html блоки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="html-block-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактровать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
                'title',
                'position',
                'status',
                'ordering',
                'updated_at',
                'created_at',
            ],
        ]
    ) ?>

</div>
