<?php

namespace common\modules\catalog\controllers;

use common\components\BaseController;
use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => Product::find()->active(),
                'pagination' => [
                    'pageSize' => 12,
                ],
            ]
        );

        return $this->render(
            'index',
            compact(
                'dataProvider',
            )
        );
    }

    public function actionCategoryView($id)
    {
        $model = $this->findCategory($id);
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => Product::find()->active()->category($id),
                'pagination' => [
                    'pageSize' => 12,
                ],
            ]
        );

        return $this->render(
            'category_view',
            [
                'model'        => $model,
                'dataProvider' => $dataProvider,
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
        return $this->render(
            'product_view',
            [
                'model' => $model,
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
