<?php

namespace common\modules\catalog\controllers;

use common\components\BaseController;
use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use Yii;
use yii\web\NotFoundHttpException;

class DefaultController extends BaseController
{
    public function actionIndex($page = 1)
    {
        $perPage = 12;
        $offset = $page - 1;
        $categories = Category::roots();
        $productCount = Product::find()->active()->count();
        $products = Product::find()->with(['category'])->active()->limit($perPage)->offset($offset)->all();

        return $this->render(
            'index',
            compact(
                'categories',
                'productCount',
                'products',
            )
        );
    }

    public function actionCategoryView($id)
    {
        $model = $this->findCategory($id);

        return $this->render(
            'category_view',
            [
                'model'    => $model,
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
    protected function findCategory($id)
    {
        if (($model = Category::find()->with(['propertyCategories', 'products', 'seo'])->where(
                ['category.id' => $id]
            )->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Страница не существует или была удалена.'));
        }
    }

    public function actionProductView($id)
    {
        /**@var $model Product */
        $model = $this->findItem($id);
        dd($model->properties);
        $formattedProperties = [];
        $defaultProperty = null;
        $properties = $model->properties;
        if ($properties) {
            $tmp = [];
            foreach ($properties as $property) {
                if ($property->default) {
                    $defaultProperty = $property;
                }
                $tmp[$property->property_category_id][] = $property;
            }
            foreach ($tmp as $category) {
                foreach ($category as $item) {
                    $formattedProperties[] = $item;
                }
            }
            if ($defaultProperty == null) {
                $defaultProperty = $formattedProperties[0];
            }
        }
        return $this->render(
            'product_view',
            [
                'model'           => $model,
                'properties'      => $formattedProperties,
                'defaultProperty' => $defaultProperty,
            ]
        );
    }

    protected function findItem($id)
    {
        if (($model = Product::findById($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Страница не существует или была удалена.'));
        }
    }

    public function actionSetViewType($type)
    {
        Yii::$app->session->set('viewType', $type);

        return $this->redirect(Yii::$app->request->referrer);
    }
}
