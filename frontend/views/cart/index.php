<?php
/**
 * Created by topalek
 * Date: 14.01.2021
 * Time: 16:20
 *
 * @var $this View
 */

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**@var $items array */
$this->title = 'Cart';
//$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-index pt-3 mb-3">
    <?php
    if (!empty($items)): ?>
        <div class="card">
            <div class="card-header">
                <h3>Cart items</h3>
            </div>
            <div class="card-body p-0">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>QTY</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($items as $item) :?>
                        <tr class="cart-item-row">
                            <td scope="row"><?= $item['name'] ?></td>
                            <td><?= Product::imgPreview($item['image']) ?></td>
                            <td><?= $item['price'] ?></td>
                            <td>
                                <?= Html::input(
                                    'number',
                                    'qty',
                                    $item['quantity'],
                                    [
                                        'class'    => 'form-control item-quantity',
                                        'style'    => 'width:80px',
                                        'min'      => 1,
                                        'data-url' => Url::to(['cart/update-count']),
                                        'data-id'  => $item['id'],
                                    ]
                                ) ?>
                            </td>
                            <td><?= $item['total_price'] ?></td>
                            <td><?= Html::a(
                                    '<i class="fa fa-trash-o"></i>',
                                    ['cart/delete', 'id' => $item['id']],
                                    [
                                        'class'        => 'btn btn-outline-danger btn-sm delete-from-cart',
                                        'data-method'  => 'post',
                                        'data-confirm' => 'Are you sure you want to remove this item from cart?',
                                    ]
                                ) ?></td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>

                <div class="col">
                    <p class="text-right mt-3">
                        <?= Html::a('Checkout', ['/cart/checkout'], ['class' => 'btn btn-primary']) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php
    else: ?>
        <div class="row">
            <div class="col">
                <p class="text-muted">
                    Cart is empty!
                </p>
            </div>
        </div>
    <?php
    endif; ?>
</div>

<style>
    .grid {
        display: grid;
        grid-gap: 10px;
        align-items: center;
    }

    .grid.col6 {
        grid-template-columns: repeat(6, 1fr);
    }

    .grid.row-a {
        grid-template-rows: auto;
    }
</style>
