<?php


use yii\bootstrap4\Nav;
use yii\web\View;

/** @var View $this */
$cartCount = $this->params['cartItemCount'];
$items = [];

$items[] = ['label' => 'About', 'url' => ['/site/about']];
$items[] = [
    'label'       => 'Cart <span class="badge badge-danger cart-count">' . $cartCount . '</span>',
    'encode'      => false,
    'url'         => ['/cart/index'],
    'linkOptions' => ['class' => 'cart'],
];
if (Yii::$app->user->isGuest) {
    $items[] = ['label' => 'Login', 'url' => ['/site/login']];
    $items[] = ['label' => 'Signup', 'url' => ['/site/signup']];
} else {
    $items[] = [
        'label'           => Yii::$app->user->identity->getDisplayName(),
        'items'           => [
            [
                'label' => 'Profile',
                'url'   => ['/profile'],
            ],
            [
                'label'       => 'Logout',
                'url'         => ['/site/logout'],
                'linkOptions' => [
                    'data-method' => 'post',
                ],
            ],
        ],
        'dropdownOptions' => ['class' => 'dropdown-menu-right'],
    ];
}

?>
<?= Nav::widget(['items' => $items,]) ?>

