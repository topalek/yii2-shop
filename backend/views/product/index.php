<?php

use common\modules\catalog\models\ProductSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товари';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title_ru',
//            'title_ru',
//            'title_en',
                'description_ru:ntext',
                // 'description_ru:ntext',
                // 'description_en:ntext',
                'price',
                // 'main_img',
                [
                    'attribute' => 'category_id',
                    'value'     => function ($model) {
                        return $model->category->title_ru;
                    },
                ],
                // 'status',
                // 'updated_at',
                // 'created_at',

                [
                    'class'   => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($index, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                $model->getSeoUrl(),
                                ['target' => '_blank', 'title' => 'Просмотр']
                            );
                        },
                    ],
                ],
            ],
        ]
    ); ?>

</div>
