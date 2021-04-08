<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\page\models\Page */

$this->title = $model->getMlTitle();
//$this->params['breadcrumbs'][] = ['label' => 'Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="page-view text-view">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= $model->getMlContent() ?>
        </div>
    </div>
</div>
