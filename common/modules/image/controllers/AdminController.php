<?php

namespace common\modules\image\controllers;

use common\components\BaseAdminController;
use common\modules\image\models\Image;
use common\modules\image\models\ImageSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AdminController implements the CRUD actions for Image model.
 */
class AdminController extends BaseAdminController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'delete'       => ['post'],
                        'delete-photo' => ['delete'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Image models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImageSearch();
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
     * Displays a single Image model.
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
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Сторінка не існує або була видалена'));
        }
    }

    /**
     * Creates a new Image model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Image();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
     * Updates an existing Image model.
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
            return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing Image model.
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

    public function actionDeleteImage($id)
    {
        if (\Yii::$app->request->isDelete) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model = $this->findModel($id);
            if ($model) {
                if ($model->delete()) {
                    return ['status' => true];
                }
                return ['status' => false];
            }
            return ['status' => false];
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Сторінка не існує або була видалена'));
        }
    }
}
