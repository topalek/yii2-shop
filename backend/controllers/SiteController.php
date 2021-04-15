<?php

namespace backend\controllers;

use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use common\components\BaseAdminController;
use common\models\forms\LoginForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class SiteController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main-login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    'Sorry, we are unable to reset password for the provided email address.'
                );
            }
        }

        return $this->render(
            'requestPasswordResetToken',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render(
            'resetPassword',
            [
                'model' => $model,
            ]
        );
    }

    public function actionClearCache()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', 'Виконано!');
        return $this->redirect(Yii::$app->request->referrer);
    }
}
