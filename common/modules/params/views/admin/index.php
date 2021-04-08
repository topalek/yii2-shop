<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\params\models\ParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Параметри';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="params-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('Добавить параметр', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'sys_name',
                'value:ntext',

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons'  => [
                        'delete' => function ($url, $model) {
                            return ($model->required) ? false : Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                $url,
                                [
                                    'data-pjax'    => 0,
                                    'data-method'  => 'post',
                                    'data-confirm' => 'Ви уверенны, что хотите удалить этот элемент?',
                                    'title'        => 'Удалить',
                                    'aria-label'   => 'Удалить',
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]
    ); ?>

</div>
