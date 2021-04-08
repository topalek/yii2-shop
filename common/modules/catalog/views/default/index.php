<?php
/**
 * @var $this     \yii\web\View
 * @var $category \common\modules\catalog\models\Category
 */

$this->title = Yii::t('site', 'Каталог');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalog-index">
    <h1><?= $this->title ?></h1>
    <div class="row list-view">
        <?php
        if ($categories) {
            foreach ($categories as $category) {
                echo $this->render('_category_view', ['model' => $category]);
            }
        }
        ?>
    </div>
</div>
