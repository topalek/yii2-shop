<?php
/**
 * Created by Yatskanych Oleksandr.
 *
 * @var $modelObject \yii\db\ActiveRecord
 */

namespace common\modules\image\widgets;

use common\modules\image\models\Image;
use http\Exception\InvalidArgumentException;
use ReflectionClass;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class InputWidget extends Widget
{
    public $model,
        $attribute,
        $options = [],
        $pluginOptions = [],
        $pluginEvents = [],
        $previewWidth = 250,
        $previewHeight = 150,
        $initialPreviewMethod = null,
        $deleteBtnOnPreview = true;

    public function init()
    {
        if (empty($this->attribute)) {
            throw new InvalidArgumentException("Invalid or empty \"{$this->attribute}\"");
        }

        if (empty($this->model)) {
            throw new InvalidArgumentException("Invalid or empty \"{$this->model}\"");
        }

        if ($this->deleteBtnOnPreview) {
            $deleteBtn = '<span class="delete-photo"><i class="glyphicon glyphicon-trash"></i></span>';
        } else {
            $deleteBtn = '';
        }

        $modelObject = $this->model;
        $initialPreview = '';
        if (!$modelObject->isNewRecord) {
            if (method_exists($modelObject, 'getImagesPreview')) {
                $initialPreview = $modelObject->getImagesPreview($this->previewWidth, $this->previewHeight);
            } elseif ($this->initialPreviewMethod != null) {
                $initialPreview = call_user_func_array(
                    [$modelObject, $this->initialPreviewMethod],
                    [$this->previewWidth, $this->previewHeight]
                );
            } else {
                $initialPreview = Image::getImagesPreview(
                    $modelObject->id,
                    $modelObject::getModelName(),
                    $this->previewWidth,
                    $this->previewHeight
                );
            }
        }

        $defaultOptions = [
            'accept'   => 'image/*',
            'multiple' => true,
            'language' => 'ru',
        ];
        $defaultPluginOptions = [
            'allowedFileExtensions' => ['jpg', 'png', 'jpeg', 'gif', 'bmp'],
            'overwriteInitial'      => false,
            'maxFileCount'          => 30,
            'autoReplace'           => true,
            'showCaption'           => false,
            'showRemove'            => false,
            'showUpload'            => false,
            'showCancel'            => false,
            'browseClass'           => 'btn btn-primary btn-block',
            'browseIcon'            => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel'           => 'Выбрать фото',
            'initialPreview'        => $initialPreview,
            'previewTemplates'      => [
                'image' => '<div class="file-preview-frame krajee-default file-preview-initial kv-preview-thumb" id="{previewId}">
                    <img src="{data}" style="width:' . $this->previewWidth . 'px" class="file-preview-image" title="{caption}" alt="{caption}"/>
                    ' . $deleteBtn . '</div>',
            ],
        ];

        $this->options = ArrayHelper::merge($defaultOptions, $this->options);
        $this->pluginOptions = ArrayHelper::merge($defaultPluginOptions, $this->pluginOptions);

        parent::init();
    }

    public function run()
    {
        $model = $this->model;
        $reflect = new ReflectionClass($model::className());
        $modelClassName = Inflector::underscore($reflect->getShortName());
        $fieldClass = 'field-' . $modelClassName . '-' . strtolower($this->attribute);

        return $this->render(
            'input_widget',
            [
                'model'         => $this->model,
                'attribute'     => $this->attribute,
                'options'       => $this->options,
                'pluginOptions' => $this->pluginOptions,
                'fieldClass'    => $fieldClass,
            ]
        );
    }
}
