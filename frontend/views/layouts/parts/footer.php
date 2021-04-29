<?php

use frontend\widgets\SitePhonesWidget;
use yii\bootstrap\Nav;

?>
<!-- Footer Section Begin -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="footer-content">
                    <div class="footer__logo">
                        <a href="#"><img src="/img/logo.svg" alt="<?= Yii::$app->name ?>"></a>
                    </div>
                    <div class="footer__nav">
                        <?= Nav::widget(['items' => $navItems, 'options' => ['class' => 'footer-nav']]) ?>
                    </div>
                    <div class="footer__contacts">
                        <?= SitePhonesWidget::widget(['containerOptions' => ['class' => 'footer-phone']]) ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="footer__copyright__text">
                    <p>Copyright © <?= Yii::$app->name ?> | <?= date('Y') ?> All rights reserved </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->
