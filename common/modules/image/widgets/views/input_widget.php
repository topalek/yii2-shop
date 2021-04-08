<?php
/**
 * @var yii\web\View $this
 * @var              $pluginOptions array
 * @var              $attribute     string
 * @var              $fieldClass    string
 * @var              $model         \yii\db\ActiveRecord
 */

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="form-group <?= $fieldClass ?>">
        <?= Html::activeLabel(
            $model,
            $attribute,
            ['class' => 'control-label', 'value' => $model->getAttributeLabel($attribute)]
        ) ?>
        <?= FileInput::widget(
            [
                'model'         => $model,
                'attribute'     => ($options['multiple'] == true) ? $attribute . '[]' : $attribute,
                'options'       => $options,
                'pluginOptions' => $pluginOptions,
                'pluginEvents'  => [
                    'fileloaded' => 'function(event, file, previewId, index, reader)
            {
                $("#"+previewId).prependTo(".' . $fieldClass . ' .file-preview .file-preview-thumbnails");
                $(".files input").last().attr("data-id",previewId);
                var maxHeight = 0;
                $(".file-preview img").each(function(i,e){
                    var itemHeight = parseInt($(e).height());
                    if(itemHeight > maxHeight)
                    {
                        maxHeight = itemHeight;
                    }
                });
                if(maxHeight != 0)
                {
                    $(".file-preview img").css("max-height",maxHeight);
                }
            }',
                ],
            ]
        ) ?>
    </div>
<?php
$deleteAction = Url::toRoute(['/image/admin/delete-image']);
$css = <<<CSS
.fileinput-remove{display:none;}
.file-preview-frame img{display: block; margin-bottom: 5px;}
.file-preview-frame .delete-photo{display: block; cursor: pointer; color: #db6967; padding-top: 2px}
.file-preview-frame .delete-photo:hover{background: #db6967; color: #fff;}
.file-footer-caption, .file-upload-indicator{display: none;}
.file-thumbnail-footer{display: none;}
CSS;
$this->registerCss($css);

$this->registerJs(
    <<<JS
    $(document).on('click','.delete-photo',function(e){
        var element = $(this).parents('.file-preview-frame'),
            imgId = $(this).data('id'),
            previewId = $(element).attr('id'),
            requestData = {_csrf: yii.getCsrfToken()},
            href = '$deleteAction'+'?id='+imgId;

        if(typeof(imgId) == 'undefined')
        {
            $(element).remove();
            if($('.file-preview-frame').size() == 0)
            {
                $('.files input').remove();
            }
            else {
                $('.files input[data-id='+previewId+']').remove();
            }
            return false;
        }

        if($(this).data('delete-url'))
        {
            href = $(this).data('delete-url');
            requestData = $.extend(requestData,$(this).data());
        }

        $.ajax({
            url: href,
            type: 'delete',
            dataType: 'json',
            data: requestData,
            success: function(result)
            {
                if(result.status == true)
                {
                    $(element).remove();
                    if($('.file-preview-frame').size() == 0)
                    {
                        $('.files input').remove();
                    }
                }
            }
        });
    });
JS
);
