<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                    $this
 * @var common\modules\page\models\Page $model
 */

$this->title = Html::encode($model->title);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="col-lg-12">
        <div class="page-view">
            <h1><?= $this->title ?></h1>
            <div>
                <?= $model->content ?>
            </div>
        </div>
    </div>
</div>
