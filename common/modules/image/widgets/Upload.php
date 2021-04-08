<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 08.10.14
 * Time: 12:49
 */

namespace common\modules\image\widgets;


use common\modules\image\assets\MultipleUploadAsset;
use common\modules\image\assets\UploadAsset;
use common\modules\image\models\Image;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class Upload extends InputWidget
{

    /**
     * @var string Идентификатор шаблона с разметкой для плагина.
     * Параметр позволяет инициализировать плагин виджета с собсвенной разметкой.
     */
    public $selector = '#uploader';

    public $multiple = false;

    public $uploadBtnText = 'Выбрать изображение';

    /**
     * @var string Название файлового поля.
     * Соответсвенно так же будет называтся $_FILES переменная с переданными файлами.
     */
    public $fileVar = 'file';

    /**
     * Настройки виджета
     *
     * @var array {@link https://github.com/RubaXa/jquery.fileapi/ FileAPI options}.
     */
    public $settings = [];

    /**
     * @var array Настройки виджета по умолчанию.
     */
    protected $_defaultSettings;

    /**
     * @var array Настройки по умолчанию для виджета с одиночной загрузкой.
     */
    protected $_defaultSingleSettings = [
        'autoUpload' => false,
    ];

    /**
     * @var array Настройки по умолчанию для мульти-загрузочного виджета.
     */
    protected $_defaultMultipleSettings = [
        'autoUpload' => false,
        'maxSize'    => 'FileAPI.MB*30',
        'accept'     => 'image/*',
        'elements'   => [
            'list' => '.uploader-files',
            'file' => [
                'tpl'      => '.uploader-file-tpl',
                'progress' => '.uploader-file-progress-bar',
                'preview'  => [
                    'el'              => '.uploader-file-preview',
                    'width'           => 255,
                    'height'          => 170,
                    'keepAspectRatio' => true,
                ],
                'upload'   => [
                    'show' => '.uploader-file-progress',
                ],
                'complete' => [
                    'hide' => '.uploader-file-progress',
                ],
            ],
        ],
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->selector = $this->selector . $this->getId();

        $request = Yii::$app->getRequest();
        // Регистрируем переводы виджета.
        //        $this->registerTranslations();
        // Если CSRF защита включена, добавляем токен в запросы виджета.
        if ($request->enableCsrfValidation) {
            $this->settings['data'][$request->csrfParam] = $request->getCsrfToken();
        }

        $this->settings['url'] = '0';

        //        $this->settings['url'] = Url::toRoute(['/image/default/create','model_id'=>$this->model->id,'model_name'=>$this->getOwnerModelName()]);

        if ($this->multiple == true) {
            $this->settings['multiple'] = true;
            //            $this->settings['url'] = Url::toRoute(['/image/default/create','model_id'=>$this->model->id,'model_name'=>$this->getOwnerModelName(),'multiple'=>true]);
        }

        // Определяем настройки по умолчанию
        if (isset($this->settings['multiple']) && $this->settings['multiple'] === true) {
            $this->_defaultSettings = $this->_defaultMultipleSettings;
        } else {
            $this->_defaultSettings = $this->_defaultSingleSettings;
        }
        // Определяем обработчики событий виджета
        $this->registerCallbacks();
        // Объединяем настройки виджета
        $this->settings = array_merge($this->_defaultSettings, $this->settings);
    }

    /**
     * Определяем обработчики событий виджета.
     */
    public function registerCallbacks()
    {
        // Определяем мульти-загрузку
        if (isset($this->settings['multiple']) && $this->settings['multiple'] === true) {
            $this->options['id'] = $this->options['id'] . '-{%key}';
            $this->options['value'] = '{%value}';
            $input = $this->hasModel() ? Html::activeHiddenInput(
                $this->model,
                '[{%key}]' . $this->attribute,
                $this->options
            ) : Html::hiddenInput('[{%key}]' . $this->name, $this->value, $this->options);
            $this->_defaultSettings['onFileComplete'] = new JsExpression(
                "function (evt, uiEvt) {
				if (uiEvt.result.error) {
					alert(uiEvt.result.error);
				} else {
					var uinput = '$input',
					    uid = FileAPI.uid(uiEvt.file),
					    ureplace = {
					    	'{%key}' : indexKey,
					    	'{%value}' : uiEvt.result.name
					    };
					uinput = uinput.replace(/{%key}|{%value}/gi, function (index) {
						return ureplace[index];
					});
			        ufile = jQuery(this).find('div[data-fileapi-id=\"' + uid + '\"] .uploader-file-fields').html(uinput);
			        var deleteLink = '<a href=\"/image/default/delete-image?id='+uiEvt.result.id+'\" class=\"delete-image\" data-id=\"'+uiEvt.result.id+'\"><i class=\"glyphicon glyphicon-trash\"></i></a>';
			        var firstLink = '<span class=\"btn btn-success btn-sm\">Главное</span>';
			        if(uiEvt.result.is_main!=true)
			            firstLink = '<a href=\"/image/default/set-as-main?id='+uiEvt.result.id+'\" class=\"btn btn-sm btn-default set-as-main\">Сделать главным</a>';
			        $('div[data-fileapi-id=\"' + uid + '\"] .uploader-file-preview').append(firstLink+' '+deleteLink);
				}
			}"
            );
        } else {
            $inputId = '#' . $this->options['id'];
            $this->_defaultSettings['onFileComplete'] = new JsExpression(
                "function (evt, uiEvt) {
				if (uiEvt.result.error) {
					alert(uiEvt.result.error);
				} else {
					jQuery(this).find('$inputId').val(uiEvt.result.name);
				}
			}"
            );
            $this->_defaultSettings['onSelect'] = new JsExpression(
                "function (event, data) {
				console.log('selected');
			}"
            );
        }
    }

    /**
     * Регистрируем переводы виджета.
     */
    //    public function registerTranslations()
    //    {
    //        $i18n = Yii::$app->i18n;
    //        $i18n->translations['extensions/fileapi/*'] = [
    //            'class' => 'yii\i18n\PhpMessageSource',
    //            'sourceLanguage' => 'ru',
    //            'basePath' => '@app/extensions/fileapi/messages',
    //            'fileMap' => [
    //                'extensions/fileapi/fileapi' => 'fileapi.php',
    //            ],
    //        ];
    //    }

    /**
     * Локальная функция перевода виджета.
     *
     * @param string      $category Категория перевода
     * @param string      $message  Сообщение которое нужно перевести
     * @param array       $params   Массив параметров которые будут заменены на их шаблоны в сообщении
     * @param string|null $language Язык перевода. В случае null, будет использован текущий [[\yii\base\Application::language|язык приложения]].
     */
    //    public static function t($category, $message, $params = [], $language = null)
    //    {
    //        return Yii::t('extensions/fileapi/' . $category, $message, $params, $language);
    //    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if (isset($this->settings['multiple']) && $this->settings['multiple'] === true) {
            $images = [];
            if (!$this->model->isNewRecord) {
                $images = Image::getModelImages($this->model->id, $this->getOwnerModelName(), 255, 170);
            }
            echo $this->render(
                'multiple',
                ['selector' => $this->getId(), 'fileVar' => $this->fileVar, 'images' => $images]
            );
        } else {
            $input = $this->hasModel() ? Html::activeHiddenInput(
                $this->model,
                $this->attribute,
                $this->options
            ) : Html::hiddenInput($this->name, $this->value, $this->options);
            echo $this->render(
                'single',
                ['selector' => $this->getId(), 'input' => $input, 'fileVar' => $this->fileVar]
            );
        }
    }

    /**
     * Регистрируем AssetBundle-ы виджета.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $csrf = Yii::$app->getRequest()->getCsrfToken();
        // Инициализируем плагин виджета
        $this->registerMainClientScript();
        // В случае мульти-загрузки добавляем индекс переменную.
        if (isset($this->settings['multiple']) && $this->settings['multiple'] === true) {
            // Регистрируем мульти-загрузочный бандл виджета
            MultipleUploadAsset::register($view);
            $view->registerJs("var indexKey = 0;");
            $script = <<<JS
            jQuery(document).on('click','$this->selector .delete-image',function(e){
                e.preventDefault();
                var el = $(this).parents('.col-sm-3');
                $.ajax({
                    url: this.href,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {_csrf: '$csrf'},
                    success: function(data)
                    {
                        if(data.result==true)
                            $(el).remove();
                    }
                });
            });
            jQuery(document).on('click','$this->selector .set-as-main',function(e){
                e.preventDefault();
                var el = $(this);
                $.ajax({
                    url: this.href,
                    type: 'POST',
                    dataType: 'json',
                    data: {_csrf: '$csrf'},
                    success: function(data)
                    {
                        if(data.result==true)
                        {
                            var oldMain = $(el).parents('.uploaded-files').find('.btn-success');
                            var id = $(oldMain).next().attr('data-id');
                            $(oldMain).hide();
                            $(oldMain).before('<a href="/image/default/set-as-main?id='+id+'" class="btn btn-default btn-sm set-as-main">Сделать главным</a>');
                            $(oldMain).remove();
                            $(el).hide().after('<span class="btn btn-success btn-sm">Главное</span>').remove();
                        }
                    }
                });
            });
JS;
            $view->registerJs($script);
        } else {
            // Регистрируем стандартный бандл виджета
            UploadAsset::register($view);
        }
    }

    /**
     * Инициализируем Javascript плагин виджета
     */
    protected function registerMainClientScript()
    {
        $view = $this->getView();
        $options = Json::encode($this->settings);
        // Инициализируем плагин виджета
        $view->registerJs("jQuery('$this->selector').fileapi($options);");
    }

    protected function getOwnerModelName()
    {
        $ref = new \ReflectionClass($this->model);
        return $ref->getShortName();
    }
}
