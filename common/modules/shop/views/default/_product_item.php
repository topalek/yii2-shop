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
        <h6><?= Html::a($model->getMlTitle(), $model->getSeoUrl(), ['class' => 'product-title_card']) ?></h6>
        <div class="price-box">
            <h5 class="price"><?= Yii::$app->formatter->asCurrency($model->price, 'UAH') ?></h5>
            <?= Html::a(
                '<i class="fa fa-shopping-cart" aria-hidden="true"></i>',
                ['/shop/default/add-to-cart', 'id' => $model->id],
                [
                    'class' => 'add-to-cart btn btn-primary',
                    'title' => Yii::t('shop', 'В корзину'),
                ]
            ) ?>
        </div>
    </div>
</div>
