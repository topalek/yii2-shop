<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Category */

$this->title = 'Создать категорію каталогу';
$this->params['breadcrumbs'][] = ['label' => 'Категорії каталогу', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
