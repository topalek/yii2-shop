<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 22.03.16
 * Time: 23:23
 *
 * @var $model        \common\modules\catalog\models\Category
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  \common\modules\catalog\models\ProductSearch
 * @var $this         \yii\web\View
 */

use common\modules\catalog\models\Product;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = $model->getMlTitle();
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Каталог'), 'url' => ['/catalog/default/index']];
foreach ($model->parents()->all() as $root) {
    $this->params['breadcrumbs'][] = ['label' => $root->getMlTitle(), 'url' => $root->getSeoUrl()];
}
$this->params['breadcrumbs'][] = $this->title;
$propertyCategories = $model->propertyCategories;

$viewType = Yii::$app->session->get('viewType', 'block');

if ($viewType == Product::VIEW_TYPE_LIST) {
    $listTypeLink = Html::tag(
        'span',
        '<i class="fa fa-th-list"></i>',
        [
            'title' => Yii::t('task', 'Списком'),
            'class' => 'active',
        ]
    );
    $blockTypeLink = Html::a(
        '<i class="fa fa-th-large"></i>',
        ['/catalog/default/set-view-type', 'type' => 'block'],
        [
            'title' => Yii::t('task', 'Блоками'),
            'class' => ($viewType == Product::VIEW_TYPE_BLOCK) ? 'active' : '',
        ]
    );
} else {
    $listTypeLink = Html::a(
        '<i class="fa fa-th-list"></i>',
        ['/catalog/default/set-view-type', 'type' => 'list'],
        [
            'title' => Yii::t('task', 'Списком'),
            'class' => ($viewType == Product::VIEW_TYPE_LIST) ? 'active' : '',
        ]
    );
    $blockTypeLink = Html::tag(
        'span',
        '<i class="fa fa-th-large"></i>',
        [
            'title' => Yii::t('task', 'Блоками'),
            'class' => 'active',
        ]
    );
}

$sortText = Yii::t('site', 'Сортировка:');
?>
<div class="catalog-category-view">
    <div class="row">
        <?php
        if (!empty($propertyCategories)): ?>
            <div class="col-sm-2">
                <?= $this->render(
                    '_search',
                    [
                        'model'              => $searchModel,
                        'categoryModel'      => $model,
                        'propertyCategories' => $propertyCategories,
                    ]
                ); ?>
            </div>
        <?php
        endif; ?>

        <div class="<?= (!empty($propertyCategories)) ? 'col-sm-10' : 'col-sm12' ?>">
            <?= ListView::widget(
                [
                    'dataProvider' => $dataProvider,
                    'itemOptions'  => [
                        'class' => ($viewType == Product::VIEW_TYPE_BLOCK) ?
                            'item block-type col-sm-4 col-xs-6 col-xxs-12' :
                            'item list-type col-sm-12',
                    ],
                    'itemView'     => function ($model) use ($viewType) {
                        return Yii::$app->view->render('_item_view', ['model' => $model, 'viewType' => $viewType]);
                    },
                    'layout'       => '<div class="sorter row">
                                <div class="col-sm-6 sort-links"><span>' . $sortText . '</span>' . $searchModel->sort->link(
                            'title'
                        ) . $searchModel->sort->link('price') . '</div>
                                <div class="col-sm-6 text-right view-type-toggle">' . $blockTypeLink . $listTypeLink . '</div>
                            </div>
                            <div class="row">{items}</div>
                            <div class="text-center">{pager}</div>',
                ]
            ) ?>

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
    </div>
</div>




