<?php

namespace common\modules\params\controllers;

use common\components\BaseAdminController;
use common\modules\params\models\Params;
use common\modules\params\models\ParamsSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * AdminController implements the CRUD actions for Params model.
 */
class AdminController extends BaseAdminController
{
    //    public function behaviors()
    //    {
    //        return [
    //            'verbs' => [
    //                'class' => VerbFilter::class,
    //                'actions' => [
    //                    'delete' => ['post'],
    //                ],
    //            ],
    //        ];
    //    }

    /**
     * Lists all Params models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ParamsSearch();
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
     * Displays a single Params model.
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
     * Finds the Params model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Params the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Params::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Params model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Params();

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
     * Updates an existing Params model.
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
     * Deletes an existing Params model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->required) {
            Yii::$app->session->setFlash('humane', 'Системний параметр не возможно удалить');
            return $this->redirect(['index']);
        } else {
            $model->delete();
        }

        return $this->redirect(['index']);
    }
}
