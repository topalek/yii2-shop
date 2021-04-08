<?php

namespace common\modules\htmlBlock\controllers;

use common\components\BaseAdminController;
use common\modules\htmlBlock\models\HtmlBlock;
use common\modules\htmlBlock\models\HtmlBlockSearch;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * AdminController implements the CRUD actions for HtmlBlock model.
 */
class AdminController extends BaseAdminController
{
    public function actionUpload()
    {
        $pic = UploadedFile::getInstanceByName('file');
        if (
            $pic->type == 'image/png'
            || $pic->type == 'image/jpg'
            || $pic->type == 'image/gif'
            || $pic->type == 'image/jpeg'
            || $pic->type == 'image/pjpeg'
        ) {
            $name = md5(time()) . '.jpg';
            if (!file_exists(Yii::$app->basePath . '/../uploads/html/')) {
                FileHelper::createDirectory(Yii::$app->basePath . '/../uploads/html/', 0777);
            }
            if (!file_exists(Yii::$app->basePath . '/../uploads/html/imperavi')) {
                FileHelper::createDirectory(Yii::$app->basePath . '/../uploads/html/imperavi', 0777);
            }

            if ($pic->saveAs(Yii::$app->basePath . '/../uploads/html/imperavi/' . $name)) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'filelink' => '/uploads/html/imperavi/' . $name,
                ];
            }
        }
    }

    /**
     * Lists all HtmlBlock models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HtmlBlockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single HtmlBlock model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Finds the HtmlBlock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return HtmlBlock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HtmlBlock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Данная страница не существует.');
        }
    }

    /**
     * Creates a new HtmlBlock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HtmlBlock();
        $model->status = true;
        $model->redactor_mode = true;
        $model->ordering = $this->getMaxOrder($model->tableName()) + 1;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('humane', 'Сохранено');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing HtmlBlock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('humane', 'Сохранено');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing HtmlBlock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionToggleStatus()
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel(Yii::$app->request->get('id'));
            if ($model->status == $model::STATUS_PUBLISHED) {
                $status = $model::STATUS_NOT_PUBLISHED;
            } else {
                $status = $model::STATUS_PUBLISHED;
            }
            $model->status = $status;
            if ($model->update(false, ['status'])) {
                echo 'Сохранено';
            } else {
                echo 'Произошла ошибка';
            }
            Yii::$app->end();
        } else {
            throw new NotFoundHttpException('Данная страница не существует.');
        }
    }
}
