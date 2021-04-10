<?php

namespace backend\controllers;

use common\components\BaseController;
use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use common\modules\catalog\models\ProductSearch;
use Yii;
use yii\web\NotFoundHttpException;

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $categories = Category::getRoots();

        return $this->render('index', ['categories' => $categories]);
    }

    public function actionCatalogCategoryView($id)
    {
        $model = $this->findCategory($id);
        $children = $model->getChildrenList();
        if (!$children) {
            $searchModel = new ProductSearch();
            $searchModel->catalog_category_id = $id;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render(
                'items',
                [
                    'model'        => $model,
                    'searchModel'  => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
        }
        return $this->render(
            'category_view',
            [
                'model'    => $model,
                'children' => $children,
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
        if (($model = Category::find()->with(['propertyCategories', 'seo'])->where(['catalog_category.id' => $id])->one(
            )) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Страница не существует или была удалена.'));
        }
    }

    public function actionProductView($id)
    {
        $model = $this->findItem($id);
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
