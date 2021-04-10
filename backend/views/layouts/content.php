<?php

use dmstr\widgets\Alert;
use yii\widgets\Breadcrumbs;

?>
<div class="content-wrapper">
    <section class="content-header">

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; <?= date("Y") ?>. All rights reserved.
</footer>

