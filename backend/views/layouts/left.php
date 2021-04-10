<?php

use backend\widgets\AdminMenu;

?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- search form -->
        <!--        <form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search..."/>
                        <span class="input-group-btn">
                        <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                        </button>
                      </span>
                    </div>
                </form>-->
        <!-- /.search form -->

        <?= AdminMenu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items'   => [
                    ['label' => 'Dashboard', 'options' => ['class' => 'header']],
                    [
                        'label' => 'Каталог',
                        'icon'  => 'shopping-bag',
                        'items' => [
                            ['label' => 'Категории', 'url' => ['/category']],
                            ['label' => 'Товары', 'url' => ['/product']],
                            ['label' => 'Категории характеристик', 'url' => ['/property-category']],
                            ['label' => 'Характеристики', 'url' => ['/property']],
                        ],
                    ],
                    ['label' => 'Settings', 'options' => ['class' => 'header']],
                    ['label' => 'Управление SEO', 'icon' => 'line-chart', 'url' => ['/seo/admin']],
                    ['label' => 'Страницы', 'icon' => 'file-text', 'url' => ['/page/admin']],
                    ['label' => 'Перевод', 'icon' => 'globe', 'url' => ['/translate/admin-translate']],
                    ['label' => 'Заказы', 'icon' => 'calendar-check-o', 'url' => ['/shop/order-admin']],
                    ['label' => 'Html блоки', 'icon' => 'code', 'url' => ['/htmlBlock/admin']],
                    ['label' => 'Параметры сайта', 'icon' => 'cogs', 'url' => ['/params/admin']],
                ],
            ]

        ) ?>

    </section>

</aside>
