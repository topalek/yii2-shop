<?php

/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 07.03.16
 * Time: 12:32
 *
 * @var $model \common\modules\catalog\models\Category
 * @var $this  \yii\web\View
 */
$this->title = $model->getMlTitle();
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Каталог'), 'url' => ['/catalog/default/index']];
foreach ($model->parents()->all() as $root) {
    $this->params['breadcrumbs'][] = ['label' => $root->getMlTitle(), 'url' => $root->getSeoUrl()];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-category-view">
    <div class="row list-view">
        <?php
        if ($children != null) {
            foreach ($children as $child) {
                echo $this->render('_category_view', ['model' => $child]);
            }
        }
        ?>
    </div>

    <div class="media about-category">
        <div class="media-left">
            <?= $model->getMainImg(200, 400, ['class' => 'media-object']) ?>
        </div>
        <div class="media-body">
            <h1 class="media-heading"><?= $this->title ?></h1>
            <p><?= $model->getMlContent() ?></p>
        </div>
    </div>
</div>




