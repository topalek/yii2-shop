<?php

use common\modules\catalog\models\Product;
use yii\helpers\Html;

/**
 * Created by topalek
 * Date: 13.01.2021
 * Time: 11:17
 *
 * @var $model Product
 */
?>
<div class="product__item">
    <div class="product__item__pic set-bg" data-setbg="<?= $model->getMainImgUrl() ?>"
         style="background-image: url('<?= $model->getMainImgUrl() ?>')">
    </div>
    <div class="product-info">
        <h6><?= Html::a($model->getMlTitle(), $model->getSeoUrl()) ?></h6>
        <h5><?= Yii::$app->formatter->asCurrency($model->price, 'UAH') ?></h5>
        <?= Html::a(
            '+ Add To Cart',
            ['/shop/default/add-to-cart', 'id' => $model->id],
            [
                //                    'data-method' => 'post',
                'class' => 'add-to-cart',
            ]
        ) ?>
    </div>
</div>
