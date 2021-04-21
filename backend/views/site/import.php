<?php

/**
 * Created by topalek
 * Date: 18.04.2021
 * Time: 7:15
 */

/* @var $msg string */

/* @var $this \yii\web\View */

use frontend\helpers\Html;
use kartik\widgets\FileInput;

?>
<h3>
    <?= $msg; ?>
</h3>
<?= Html::beginForm(
    '',
    'post',
    [
        'enctype' => 'multipart/form-data',
    ]
) ?>
<?= FileInput::widget(
    [
        'name'          => 'importfile',
        'pluginOptions' => [
            'showCaption'     => false,
            'showRemove'      => false,
            'showUpload'      => false,
            'browseClass'     => 'btn btn-primary btn-block',
            'browseIcon'      => '<i class="glyphicon glyphicon-camera"></i> ',
            'previewFileType' => 'any',
        ],
    ]
); ?>
<p class="mt-3">
    <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
</p>
<?= Html::endForm() ?>


