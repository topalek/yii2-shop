<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View                  $this
 * @var common\modules\seo\models\Seo $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Seo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
                'description',
                'keywords',
                'head_block:ntext',
                'external_link',
                'internal_link',
                'external_link_with_cat',
                'noindex',
                'nofollow',
                'in_sitemap',
                'is_canonical',
                'model_name',
                'model_id',
                'status',
                'updated_at',
            ],
        ]
    ) ?>

</div>
