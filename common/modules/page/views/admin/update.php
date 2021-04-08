<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\page\models\Page */

$this->title = 'Радагувати сторінку: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'Статичні сторінки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_uk, 'url' => $model->seoUrl];
$this->params['breadcrumbs'][] = 'Радагувати';
?>
<div class="page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
