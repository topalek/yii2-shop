<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 28.03.16
 * Time: 17:46
 *
 * @var $model \common\modules\catalog\models\Category
 * @var $this  \yii\web\View
 */

use yii\helpers\Html;

?>

<div class="col-sm-3 item block-type">
    <?= Html::a($model->getMainImg(), $model->getSeoUrl()) ?>
    <h4 class="text-center"><?= $model->getMlTitle() ?></h4>
</div>
