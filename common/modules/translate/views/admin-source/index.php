<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\translate\models\SourceTranslateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Source Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Source Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'category',
                'message:ntext',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]
    ); ?>

</div>
