<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                              $this
 * @var common\modules\htmlBlock\models\HtmlBlock $model
 */

$this->title = 'Редактировать блок: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Html блоки', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="html-block-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
