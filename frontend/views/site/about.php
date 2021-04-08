<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>

    <!--    <img class="lazy-load img-responsive" src="https://vencon.ua/uploads/products/main_image/GBK150LN3_4V9.jpg" srcset="https://dummyimage.com/600x400/000/fff 600w, https://dummyimage.com/300x200/ad66ad/fff 600w" width="400" height="280" alt="" data-srcset="https://vencon.ua/uploads/products/main_image/GBK150LN3_4V9.jpg?s=300x300 300w, https://vencon.ua/uploads/products/main_image/GBK150LN3_4V9.jpg?s=200x200 200w" data-loaded="true">-->
    <img alt="Elva dressed as a fairy" sizes="420px, 768px, 1027px"
         src="img/1200x800.jpg "
         srcset="img/300x200.jpg 300w, img/600x400.jpg 600w, img/1200x800.jpg 800w">

</div>
