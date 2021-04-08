<?php

use kartik\widgets\SwitchInput;
use kartik\widgets\TouchSpin;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                              $this
 * @var common\modules\htmlBlock\models\HtmlBlock $model
 * @var yii\widgets\ActiveForm                    $form
 */
$css = <<<CSS
textarea{
    width: 100%;
    min-height: 350px;
    resize: vertical;
}
CSS;

$this->registerCss($css);
?>

<div class="html-block-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?php
    if ($model->isNewRecord || $model->redactor_mode == true) {
        echo $form->field($model, 'content')->widget(
            yii\imperavi\Widget::class,
            [
                'id'      => 'html-content',
                'options' => [
                    'buttonSource'      => 'true',
                    'lang'              => 'uk',
                    'imageUpload'       => '/html-block/admin/upload',
                    'minHeight'         => 350,
                    'replaceDivs'       => false,
                    'paragraphize'      => false,
                    'removeDataAttr'    => false,
                    'uploadImageFields' => [
                        Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken(),
                    ],
                    'uploadFileFields'  => [
                        Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken(),
                    ],
                ],
                'plugins' => [
                    'fullscreen',
                    'fontfamily',
                    'fontsize',
                    'fontcolor',
                ],
            ]
        );
    } else {
        echo $form->field($model, 'content')->textarea();
    }
    ?>

    <?= $form->field($model, 'redactor_mode')->widget(
        SwitchInput::class,
        [
            'pluginOptions' => [
                'size'    => 'small',
                'onText'  => 'Да',
                'offText' => 'Нет',
            ],
            'pluginEvents'  => [
                "switchChange.bootstrapSwitch" => "function(event,state){
            if(state==true){
                $('#html-content').redactor({
                    buttonSource: true,
                    lang: 'uk',
                    imageUpload: '/html-block/admin/upload',
                    minHeight: 350,
                    convertDivs: false,
                    paragraphize: false,
                    removeDataAttr: false,
                    focus: true,
                    plugins: [
                        'fullscreen',
                        'fontfamily',
                        'fontsize',
                        'fontcolor'
                    ]
                });
            }
            else
                $('#html-content').redactor('core.destroy');
           }",
            ],
        ]
    ); ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'status')->widget(
        SwitchInput::class,
        [
            'pluginOptions' => [
                'size'    => 'small',
                'onText'  => 'Да',
                'offText' => 'Нет',
            ],
        ]
    ); ?>

    <?= $form->field($model, 'ordering', ['options' => ['class' => 'form-group ordering-field']])->widget(
        TouchSpin::class,
        [
            'pluginOptions' => ['verticalbuttons' => true],
        ]
    ); ?>


    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
        <?php
        if (!$model->isNewRecord): ?>
            <?= Html::a('Создать новый блок', ['create'], ['class' => 'btn btn-success']) ?>
        <?php
        endif; ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>

