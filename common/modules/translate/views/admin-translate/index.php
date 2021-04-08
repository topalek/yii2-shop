<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\translate\models\TranslateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Перевод';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить перевод', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            'Обновить список переводов',
            ['/translate/admin-translate/update-data'],
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'message',
                    'value'     => function ($data) {
                        return $data->source->message;
                    },
                ],
                [
                    'attribute' => 'language',
                    'filter'    => ['uk' => 'Українська', 'en' => 'English'],
                ],
                [
                    'attribute' => 'category',
                    'filter'    => \common\modules\translate\models\SourceTranslate::getCategoryList(),
                    'value'     => function ($data) {
                        return $data->source->category;
                    },
                ],
                [
                    'attribute' => 'translation',
                    'filter'    => [1 => 'Есть перевод', 0 => 'Нет'],
                ],

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                ],
            ],
        ]
    ); ?>

</div>
