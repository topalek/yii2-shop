<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 08.10.14
 * Time: 12:50
 */

use yii\helpers\Html;

?>
<div id="uploader<?= $selector ?>" class="uploader">
    <div class="controls">
        <div class="js-fileapi-wrapper btn btn-default">
            <div class="uploader-browse">
                <div class="btn btn-primary btn-block btn-file">
                    <i class="glyphicon glyphicon-camera"></i> <?= $this->context->uploadBtnText ?>
                    <input type="file" name="<?= $fileVar ?>" multiple accept="image/*">
                </div>
            </div>
        </div>
    </div>
    <div class="uploader-files row">
        <div class="uploader-file-tpl col-sm-3">
            <div class="uploader-file">
                <div class="uploader-file-progress">
                    <div class="progress progress-striped">
                        <div class="uploader-file-progress-bar progress-bar progress-bar-info" role="progressbar"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="uploader-file-preview"></div>
                <div class="col-sm-10 uploader-file-fields"></div>
            </div>
        </div>
    </div>
    <div class="uploaded-files clearfix row">
        <?php
        if ($images) {
            foreach ($images as $image):?>
                <div class="col-sm-3">
                    <div class="uploader-file">
                        <div class="uploader-file-preview">
                            <?= Html::img($image['url']); ?>
                            <?= ($image['is_main'] ? '<span class="btn btn-sm btn-success">Главное</span>' : Html::a(
                                'Сделать главным',
                                ['/image/default/set-as-main', 'id' => $image['id']],
                                ['class' => 'set-as-main btn btn-default btn-sm']
                            )) ?>
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-trash"></i>',
                                ['/image/default/delete-image', 'id' => $image['id']],
                                ['class' => 'delete-image', 'data-id' => $image['id']]
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php
            endforeach;
            echo '<div class="clearfix"></div>';
        }
        ?>
    </div>
</div>
