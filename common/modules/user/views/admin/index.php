<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Користувачі';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить користувача', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'username',
                //            'password',
                'email:email',
                [
                    'attribute' => 'role',
                    'value'     => function ($data) {
                        return $data->getRole();
                    },
                ],
                // 'status',
                // 'auth_key',
                // 'updated_at',
                // 'created_at',
                // 'deleted_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]
    ); ?>

</div>
