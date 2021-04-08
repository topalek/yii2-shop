<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\catalog\models\PropertyCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории характеристик';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title_uk',
                [
                    'attribute' => 'catalog_categories',
                    'header'    => 'Категории в каталоге',
                    'value'     => function ($data) {
                        return $data->catalogCategoryList;
                    },
                ],

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                ],
            ],
        ]
    ); ?>

</div>
