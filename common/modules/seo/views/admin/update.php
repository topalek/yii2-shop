<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                  $this
 * @var common\modules\seo\models\Seo $model
 */

$this->title = 'Редактировать: ' . ' ' . $model->title_uk;
$this->params['breadcrumbs'][] = ['label' => 'SEO', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="seo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
