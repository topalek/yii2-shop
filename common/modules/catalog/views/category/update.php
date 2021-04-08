<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Category */

$this->title = 'Редактировать категорію: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'Категорії каталогу', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_uk, 'url' => $model->seoUrl];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="catalog-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
