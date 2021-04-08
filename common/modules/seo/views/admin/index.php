<?php

use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View                        $this
 * @var common\modules\seo\models\SeoSearch $searchModel
 * @var yii\data\ActiveDataProvider         $dataProvider
 */

$this->title = 'SEO';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                'external_link',
                'title_ru',
                'description_ru',
                'keywords_ru',
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                ],
            ],
        ]
    ); ?>

</div>
