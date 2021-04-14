<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\shop\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    Pjax::begin(); ?>    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                'email:email',
                'phone',
                'delivery_info:ntext',
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'filter'    => $searchModel::statusList(),
                    'value'     => function ($model) {
                        return $model->getStatusLabel();
                    },
                ],
                'created_at',

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view}{delete}',
                ],
            ],
        ]
    ); ?>
    <?php
    Pjax::end(); ?></div>
