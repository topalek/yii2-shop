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
use yii\web\View;

$this->title = $user->getDisplayName() . ' profile';
?>
<div class="col">
    <div class="card">
        <h5 class="card-header">User info</h5>
        <div class="card-body">
            <?= $this->render('profile_form', ['user' => $user]) ?>
        </div>
    </div>
</div>


