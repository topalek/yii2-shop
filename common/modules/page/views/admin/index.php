<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\page\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статические страницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать страницу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                // 'title_uk',
                'title_ru',
                //            'title_en',
                [
                    'attribute' => 'content_ru',
                    'value'     => function ($data) {
                        return getShortText($data->content_ru, 600, true);
                    },
                ],
                // 'content_ru:ntext',
                // 'content_en:ntext',
                // 'status',
                // 'updated_at',
                'created_at',

                [
                    'class'   => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $model->seoUrl);
                        },
                    ],
                ],
            ],
        ]
    ); ?>

</div>
