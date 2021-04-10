<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\catalog\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $roots common\modules\catalog\models\Category[] */

$this->title = 'Категории каталога';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div id="categories-ajax-container">
        <div class="grid-view">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <td>Название</td>
                    <td>Ссылка</td>
                    <td></td>
                </tr>
                </thead>
                <?php
                foreach ($roots as $root) {
                    echo $this->render('_row', ['model' => $root]);
                } ?>
            </table>
        </div>
    </div>

</div>
