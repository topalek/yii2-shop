<?php
/**
 * Представление advanced загрузки.
 *
 * @var yii\base\View $this Представление
 */

use yii\helpers\Html;

$containerId = $selector . '-uploader';
?>
<div id="<?= $containerId ?>">
    <div id="<?= $selector; ?>" class="uploader">
        <?php
        if ($preview !== false) { ?>
            <div class="uploader-preview-cnt">
                <?php
                if ($delete === true) { ?>
                    <?php
                    if ($url !== null && $modelId) { ?>
                        <a href="#" class="uploader-delete uploader-delete-current" data-id="<?= $modelId ?>">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    <?php
                    } ?>
                    <a href="#" class="uploader-delete uploader-delete-temp hide">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                <?php
                } ?>
                <div class="uploader-preview">
                    <?php
                    if ($url !== null) { ?>
                        <img src="<?= $url ?>" alt="preview"/>
                        <?php
                    } ?>
                </div>
                <div class="old-preview" style="display: none;"></div>
            </div>
            <?php
        } ?>
        <div class="btn btn-default js-fileapi-wrapper">
            <div class="uploader-browse">
                <?= Html::button('Выбрать файл', ['class' => 'btn btn-primary']) ?>
                <input type="file" name="<?= $fileVar ?>">
            </div>
            <div class="uploader-progress">
                <div class="progress progress-striped">
                    <div class="uploader-progress-bar progress-bar progress-bar-info" role="progressbar"
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <?= $input ?>
    </div>
</div>


<?php
if ($crop !== false): ?>
    <!-- Modal -->
    <div id="modal-crop">
        <div class="modal-crop-body">
            <div class="uploader-crop"></div>
            <div class="buttons">
                <button type="button" class="btn btn-primary modal-crop-upload">Загрузить</button>
                <button type="button" class="btn btn-info"
                        onclick="$(this).parents('.themodal-overlay').remove(); $('.uploader-preview').html($('.old-preview').html())">
                    Отмена
                </button>
            </div>
        </div>
    </div>
    <!--/ Modal -->
<?php
endif;

$containerId = '#' . $containerId;
$previewWidth = $this->context->previewWidth . 'px';
$css = <<<CSS
$containerId .uploader, $containerId .uploader-preview-cnt{
width: $previewWidth;
}
.buttons{
    text-align: center;
}
.buttons button{
    display: inline-block!important;
    vertical-align: top;
    margin: 15px 5px 0;
}
.limit-info{
    padding: 3px 3px 0;
    font-size: 10px;
    clear: both;
    color: #5587aa;
    text-align: center;
    background: #fff;
}
.uploader .js-fileapi-wrapper{
    box-shadow: none!important;
}
.uploader .btn{
  padding: 0;
  border: none;
  cursor: pointer;
  height: 35px;
}
.uploader .btn:hover{
  cursor: pointer;
  background: #286090;
}
CSS;

$this->registerCss($css);
$js = <<<JS
    $('.uploader-preview img').clone().appendTo('.old-preview');
JS;
$this->registerJs($js);
?>
