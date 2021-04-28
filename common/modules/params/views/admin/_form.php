<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\params\models\Params */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="params-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'sys_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?php
    if (!$model->required) echo $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Добавить' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>

<div class="params-info">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Дополнительные параметры</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table class="table no-margin">
                    <thead>
                    <tr>
                        <th>Название параметра</th>
                        <th>Системное название</th>
                        <th>Описание</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                    <tr>
                                            <td>
                                                Email
                                            </td>
                                            <td><span class="label label-success">infoEmail</span></td>
                                            <td>
                                            </td>
                                        </tr>-->
                    <tr>
                        <td>
                            Email магазина
                        </td>
                        <td><span class="label label-success">shopEmail</span></td>
                        <td>
                            Email на который будут приходить письма о новых заказах
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Телефоны сайта
                        </td>
                        <td><span class="label label-success">sitePhones</span></td>
                        <td>
                            Телефоны указывать разделяя запятой.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
        </div>
        <!-- /.box-footer -->
    </div>
</div>
