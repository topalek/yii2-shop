<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                              $this
 * @var common\modules\htmlBlock\models\HtmlBlock $model
 */

$this->title = 'Создать Html блок';
$this->params['breadcrumbs'][] = ['label' => 'Html блоки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="html-block-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
