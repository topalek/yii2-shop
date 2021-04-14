<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\PropertyCategory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Property Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            'Delete',
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
                'title_ru',
                'title_uk',
                'title_en',
                'updated_at',
                'created_at',
            ],
        ]
    ) ?>

</div>
