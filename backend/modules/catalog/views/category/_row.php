<?php
/**
 *
 * @var $this  yii\web\View
 * @var $model common\modules\catalog\models\Category
 */

use yii\helpers\Html;

$subCategories = $model->children();
$parent = $model->parent;
$class = ($subCategories) ? 'has-sub-category open ' : '';
if ($parent) {
    $class .= 'sub-category-for-' . $parent->id;
}

$paddingLeft = 25;
if ($paddingLeft == 0) {
    $paddingLeft = '10';
}

$isRoot = $model->isRoot();
$next = $model->next()->one();
$prev = $model->prev()->one();
?>

<tr class="<?= $class ?>" data-id="<?= $model->id ?>">
    <td style="padding-left: <?= $paddingLeft ?>px;"><span class="title"><?= $model->title_ru ?></span></td>
    <td><?= $model->getSeoUrl() ?></td>
    <td>
        <?= Html::a(
            '<span class="glyphicon glyphicon-pencil"></span>',
            ['update', 'id' => $model->id],
            ['data-pjax' => 0, 'title' => 'Редактировать']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-plus"></i>',
            ['create', 'parent' => $model->id],
            ['title' => 'Добавить дочернюю', 'data-pjax' => 0]
        ) ?>
        <?= Html::a(
            '<span class="glyphicon glyphicon-trash"></span>',
            ['delete', 'id' => $model->id],
            [
                'title' => 'Удалить',
                'class' => 'ajax-link',
                'data'  => [
                    'method'  => 'post',
                    'confirm' => 'Ви уверенны?',
                ],
            ]
        ) ?>
        <?php
        if (!$isRoot) {
            if ($prev) {
                echo Html::a(
                    '<span class="glyphicon glyphicon-arrow-up"></span>',
                    ['move-up', 'id' => $model->id],
                    ['class' => 'ajax-link', 'title' => 'Поднять вверх']
                );
            }
            if ($next) {
                echo Html::a(
                    '<span class="glyphicon glyphicon-arrow-down"></span>',
                    ['move-down', 'id' => $model->id],
                    ['class' => 'ajax-link', 'title' => 'Опустить вниз']
                );
            }
        }
        ?>
    </td>
</tr>

<?php
if ($subCategories) {
    foreach ($subCategories as $subCategory) {
        echo $this->render('_row', ['model' => $subCategory]);
    }
}
?>

