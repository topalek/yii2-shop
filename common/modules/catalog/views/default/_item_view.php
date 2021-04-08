<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 22.03.16
 * Time: 23:26
 *
 * @var $model \common\modules\catalog\models\Product
 */

use yii\helpers\Html;

?>

<?php
if ($viewType == $model::VIEW_TYPE_BLOCK): ?>

    <?= Html::a($model->getMainImg(), $model->getSeoUrl()) ?>
    <h4 class="text-center"><?= $model->getMlTitle() ?></h4>
    <span class="price"><?= $model->getPrice() . ' грн' ?></span>
    <?= Html::a(Yii::t('catalog', 'Детальніше'), $model->getSeoUrl(), ['class' => 'view-more']) ?>

<?php
else: ?>

    <div class="row">
        <div class="col-sm-3">
            <?= Html::a($model->getMainImg(), $model->getSeoUrl()) ?>
        </div>
        <div class="col-sm-8">
            <h4><?= $model->getMlTitle() ?></h4>
            <span class="price"><?= $model->getPrice() . ' грн' ?></span>
            <p>
                <?= Html::a(Yii::t('catalog', 'Детальніше'), $model->getSeoUrl(), ['class' => 'view-more']) ?>
            </p>
        </div>
    </div>

<?php
endif; ?>
