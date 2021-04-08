<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Product */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Catalog Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-item-view">

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
    </p>

    <?= DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'title_uk',
                'title_ru',
                'title_en',
                'description_uk:ntext',
                'description_ru:ntext',
                'description_en:ntext',
                'price',
                'main_img',
                'catalog_category_id',
                'status',
                'updated_at',
                'created_at',
            ],
        ]
    ) ?>

</div>
