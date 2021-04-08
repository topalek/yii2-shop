<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
        <?= Html::a('Create new', ['create'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'name',
                'description:html',
                'imgPreview:html',
                'price:currency',
                'statusBadge:html',
                'created_at:datetime',
                'updated_at:datetime',
                'createdBy.username',
                'updatedBy.username',
            ],
        ]
    ) ?>

</div>
