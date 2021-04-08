<?php

/* @var $this yii\web\View */

/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \common\models\forms\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Login';
?>
<div class="col-xl-10 col-lg-12 col-md-9">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                        </div>
                        <?php
                        $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'user']]); ?>

                        <?= $form->field($model, 'username')->textInput(
                            [
                                'autofocus'   => true,
                                'class'       => 'form-control form-control-user',
                                'placeholder' => 'Enter your username',
                            ]
                        ) ?>

                        <?= $form->field($model, 'password')->passwordInput(
                            ['class' => 'form-control form-control-user', 'placeholder' => 'Password']
                        ) ?>

                        <?= $form->field($model, 'rememberMe')->checkbox(
                            ['class' => "custom-control-input"]
                        ) ?>

                        <?= Html::submitButton(
                            'Login',
                            [
                                'class' => 'btn btn-primary btn-block btn-user',
                                'name'  => 'login-button',
                            ]
                        ) ?>

                        <?php
                        ActiveForm::end(); ?>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="<?= Url::to(['/site/forgot-password']) ?>">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

