<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\image\models\ImageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Images';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Image', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'model_name',
                'model_id',
                'image',
                'is_main',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]
    ); ?>

</div>
