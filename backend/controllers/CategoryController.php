<?php

namespace backend\controllers;

use backend\extensions\fileapi\actions\DeleteAction;
use backend\extensions\fileapi\actions\UploadAction;
use common\components\BaseAdminController;
use common\modules\catalog\models\Category;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseAdminController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return ArrayHelper::merge(
            $behaviors,
            [
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ]
        );
    }

    public function actions()
    {
        return [
            'uploadTempImage' => [
                'class'     => UploadAction::class,
                'path'      => Category::mainImgTempPath(),
                'types'     => ['jpg', 'png'],
                'minHeight' => 300,
                'minWidth'  => 400,
                'maxHeight' => 1000,
                'maxWidth'  => 1000,
                'maxSize'   => 3145728,
            ],
            'deleteTempImage' => [
                'class' => DeleteAction::class,
                'path'  => Category::mainImgTempPath(),
            ],
        ];
    }

    /**
     * Lists all Category models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render(
            'index',
            [
                'roots' => Category::roots()
                //            'searchModel'  => $searchModel,
                //            'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Category model.
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param null $parent
     *
     * @return mixed
     */
    public function actionCreate($parent = null)
    {
        $model = new Category();
        $model->setScenario('create');
        $model->parentId = $parent;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
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
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $parent = $model->getParent();

        if (Yii::$app->request->isGet) {
            if ($parent) {
                $model->parentId = $parent->id;
            } else {
                $model->parentId = null;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->parentId != null) {
                if (!$parent || $model->parentId != $parent->id) {
                    $target = Category::findOne($model->parentId);
                    if ($target != null) {
                        $model->prependTo($target);
                    }
                }
                $model->save(false);
            } else {
                if (!$model->isRoot()) {
                    $model->makeRoot();
                } else {
                    $model->save(false);
                }
            }

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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->isRoot()) {
            $children = $model->children()->all();
            if ($children) {
                foreach ($children as $child) {
                    $child->delete();
                }
            }
            $model->deleteWithChildren();
        } else {
            $model->delete();
        }
        return $this->redirect(['index']);
    }

    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if (($prev = $model->prev()->one()) != null) {
            $model->insertBefore($prev);
        }
        //        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionMoveDown($id)
    {
        $model = $this->findModel($id);
        if (($prev = $model->next()->one()) != null) {
            $model->insertAfter($prev);
        }
        //        return $this->redirect(Yii::$app->request->referrer);
    }
}
