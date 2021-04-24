<?php

namespace backend\controllers;

use common\components\BaseAdminController;
use common\modules\catalog\models\Product;
use common\modules\catalog\models\ProductProperty;
use common\modules\catalog\models\ProductSearch;
use common\modules\catalog\models\Property;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends BaseAdminController
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
     * Lists all Product models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
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
     * Displays a single Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::find()->with(['category', 'properties'])->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Product();
        $model->setScenario('create');
        $model->status = $model::STATUS_PUBLISHED;
        if ($request->isPost) {
            $model->load($request->post());
            $model->originalImgFile = UploadedFile::getInstance($model, 'originalImgFile');
            $model->imgFiles = UploadedFile::getInstances($model, 'imgFiles');
        }
        if ($request->isPost && $model->save()) {
            $model->saveImg();
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
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isPost) {
            $model->load($request->post());
            $model->originalImgFile = UploadedFile::getInstance($model, 'originalImgFile');
            $model->imgFiles = UploadedFile::getInstances($model, 'imgFiles');

            if ($model->save()) {
                $model->saveImg();
                Yii::$app->session->setFlash('success', 'Сохранено');
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('danger', $model->errors);
            }
        }

        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            FileHelper::removeDirectory(Product::moduleUploadsPath() . $id);
        }

        return $this->redirect(['index']);
    }

    public function actionDepProperty()
    {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];

                $models = Property::getListByCategory($cat_id);

                foreach ($models as $modelId => $modelTitle) {
                    $out[] = ['id' => $modelId, 'name' => $modelTitle];
                }

                return ['output' => $out];
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function actionAddProperty2($product_id)
    {
        $model = new ProductProperty();
        $model->product_id = $product_id;

        if (Yii::$app->request->isGet) {
            return $this->renderAjax('_property_form', ['model' => $model]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['status' => true, 'data' => $this->renderPartial('_product_property_view', ['model' => $model])];
        } else {
            return ['status' => false, 'message' => 'Произошла ошибка. Попробуйте еще раз.'];
        }
    }

    public function actionAddProperty($product_id)
    {
        $model = new ProductProperty();
        $model->product_id = $product_id;
        $product = Product::findOne($product_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $product_id]);
        }
        return $this->render('property_form', ['model' => $model, 'product' => $product]);
    }


    public function actionUpdateProperty($property_id, $product_id)
    {
        $model = ProductProperty::find()->where(['property_id' => $property_id, 'product_id' => $product_id])->limit(
            1
        )->one();
        $product = Product::findOne($product_id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $product_id]);
        }
        return $this->render('property_form', ['model' => $model, 'product' => $product]);
    }

    public function actionDeleteProperty($property_id, $product_id)
    {
        $model = ProductProperty::find()->where(['property_id' => $property_id, 'product_id' => $product_id])->limit(
            1
        )->one();
        $model->delete();
    }

    public function actionResetPropertyForm($product_id)
    {
        $model = new ProductProperty();
        $model->product_id = $product_id;

        return $this->renderAjax('_property_form', ['model' => $model]);
    }

    public function actionSortGallery()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isPost) {
            $modelId = $request->post('modelId');
            $sort = $request->post('sort');
            $model = Product::findOne($modelId);
            $imgList = $model->additional_images;
            moveElement($imgList, $sort['oldIndex'], $sort['newIndex']);
            $model->additional_images = $imgList;
            if (!$model->save(false)) {
                return $model->errors;
            }
            return true;
        }
    }

    public function actionDeleteAdditionalImg()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $modelId = $request->post('id');
            $key = $request->post('key');
            $model = Product::findOne($modelId);
            $imgList = $model->additional_images;
            $file = explode('/', $imgList[$key]);
            $file = array_pop($file);
            @unlink($model->modelUploadsPath() . 'additional/' . $file);
            unset($imgList[$key]);
            $model->additional_images = [...$imgList];
            if (!$model->save(false)) {
                return $model->errors;
            }
            return $this->redirect(['update', 'id' => $modelId]);
        }
    }
}
