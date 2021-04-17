<?php

/**
 * Created by topalek
 * Date: 13.04.2021
 * Time: 9:54
 */

/* @var $this \yii\web\View */

/* @var $model \common\modules\catalog\models\ProductProperty */

use yii\helpers\Html;

?>
<tr class="product-property" data-id="<?= $model->id ?>">
    <td><?= $model->property->category->getMLTitle() ?></td>
    <td><?= $model->property->getMLTitle() ?></td>
    <td class="actions">
        <?= Html::a(
            '<i class="fa fa-pencil"></i>',
            ['/product/update-property', 'id' => $model->id],
            ['class' => 'update-property']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-times"></i>',
            ['/product/delete-property', 'id' => $model->id],
            ['class' => 'delete-property']
        ) ?>
    </td>
</tr>
