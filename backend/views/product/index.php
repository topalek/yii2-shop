<?php


use common\components\SwitchColumn;
use common\modules\catalog\models\Category;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\catalog\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-item-index">

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
                'id',
                // 'title_uk',
                'title_ru:html',
                //            'title_en',
                //             'description_uk:ntext',
//                'MlShortContent:ntext',
                // 'description_en:ntext',
                // 'price',
                // 'main_img',
                [
                    'attribute' => 'category_id',
                    'value'     => function ($model) {
                        return $model->category->title_ru;
                    },
                    'filter'    => Category::getList(),
                ],
                [
                    'class'     => SwitchColumn::class,
                    'attribute' => 'status',
                    'filter'    => [1 => 'Опубликован', 0 => 'Нет'],
                ],
//                 'status',
                // 'updated_at',
                // 'created_at',
                [
                    'class' => 'yii\grid\ActionColumn',
                ],
            ],
        ]
    ); ?>

</div>
