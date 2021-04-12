<?php

namespace backend\controllers;

use backend\extensions\fileapi\actions\DeleteAction;
use backend\extensions\fileapi\actions\UploadAction;
use common\components\BaseAdminController;
use common\modules\catalog\models\Category;
use common\modules\seo\behaviors\SeoBehavior;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
        $model->parent_id = $parent;

        if ($model->load(Yii::$app->request->post())) {
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
            if ($model->imgFile && $model->validate()) {
                $model->save();
                Yii::$app->session->setFlash('humane', 'Сохранено');
            }
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
        $parent = $model->parent;

        if ($model->load(Yii::$app->request->post())) {
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
            if ($model->imgFile && $model->validate()) {
                $imgName = SeoBehavior::generateSlug($model->title_ru) . '.' . $model->imgFile->extension;
                $model->main_img = $imgName;
                $model->imgFile->saveAs($model->modelUploadsPath() . $imgName);
                $model->save();
                Yii::$app->session->setFlash('humane', 'Сохранено');
            }

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
            $children = $model->children();
            if ($children) {
                Category::updateAll(['parent_id' => null], ['parent_id' => $this->id]);
            }
        }
        $model->delete();

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

    public function actionUploadImg()
    {
    }

    public function actionDeleteImg($id)
    {
        $result = false;
        $model = Category::findOne($id);
        $imgPath = $model::moduleUploadsPath() . $model->id . DIRECTORY_SEPARATOR . $model->main_img;
        if ($model->main_img && file_exists($imgPath)) {
            $model->main_img = null;
            if ($model->save(false)) {
                unlink($imgPath);
                $result = true;
            } else {
                $result = $model->errors;
            }
        }
        return Json::encode($result);
    }

}
