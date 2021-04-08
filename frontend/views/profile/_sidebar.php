<?php
/**
 * Created by topalek
 * Date: 15.01.2021
 * Time: 18:14
 *
 * @var $user    User
 * @var $address UserAddress
 * @var $this    View
 */

use common\models\User;
use common\models\UserAddress;
use yii\bootstrap4\Nav;
use yii\web\View;

?>

<div class="user-nav card header__top">
    <div class="card-header">
        <div class="nav-title text-light">Navigation</div>
    </div>
    <div class="card-body">
        <?= Nav::widget(
            [
                'items'   => [
                    [
                        'label' => 'Profile',
                        'url'   => ['/profile/index'],
                    ],
                    [
                        'label' => 'Address',
                        'url'   => ['/profile/address'],
                    ],
                    [
                        'label' => 'Orders',
                        'url'   => ['/profile/orders'],
                    ],
                ],
                'options' => [
                    'class' => 'flex-column nav-pills',
                ],
            ]
        ) ?>
    </div>
</div>
