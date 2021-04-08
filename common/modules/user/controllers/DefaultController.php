<?php

namespace common\modules\user\controllers;

use common\components\BaseController;
use common\modules\tour\models\Guide;
use common\modules\tour\models\GuideSearch;
use common\modules\tour\models\Tour;
use common\modules\tour\models\TourSearch;
use common\modules\user\models\User;
use common\modules\user\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['create', 'view'],
                'rules' => [
                    [
                        'allow'   => true,
                        'roles'   => ['?'],
                        'actions' => ['create'],
                    ],
                    [
                        'allow'   => true,
                        'roles'   => ['@'],
                        'actions' => ['view'],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    //    public function actionIndex()
    //    {
    //        $searchModel = new UserSearch();
    //        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    //
    //        return $this->render('index', [
    //            'searchModel' => $searchModel,
    //            'dataProvider' => $dataProvider,
    //        ]);
    //    }

    /**
     * Displays a single User model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Данная страница не существует.');
        }
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            $model = new User();

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
        } else {
            return $this->redirect(Url::home());
        }
    }

    /**
     * Updates an existing User model.
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
     * Deletes an existing User model.
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
}
