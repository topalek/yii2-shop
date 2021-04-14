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
        <ul class="product__hover">
            <li><a href="#"><img src="/img/icon/heart.png" alt=""></a></li>
            <li><a href="#"><img src="/img/icon/compare.png" alt=""> <span>Compare</span></a>
            </li>
            <li><a href="#"><img src="/img/icon/search.png" alt=""></a></li>
        </ul>
    </div>
    <div class="product__item__text">
        <h6><?= $model->getMlTitle() ?></h6>
        <?= Html::a(
            '+ Add To Cart',
            ['/shop/default/add-to-cart', 'id' => $model->id],
            [
//                    'data-method' => 'post',
                'class' => 'add-to-cart',
            ]
        ) ?>

        <h5><?= Yii::$app->formatter->asCurrency($model->price, 'UAH') ?></h5>
        <div class="product__color__select">
            <label for="pc-4">
                <input type="radio" id="pc-4">
            </label>
            <label class="active black" for="pc-5">
                <input type="radio" id="pc-5">
            </label>
            <label class="grey" for="pc-6">
                <input type="radio" id="pc-6">
            </label>
        </div>
    </div>
</div>
