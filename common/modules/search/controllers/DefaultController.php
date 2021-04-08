<?php

namespace common\modules\search\controllers;

use common\components\BaseController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\validators\StringValidator;

class DefaultController extends BaseController
{
    const PAGE_SIZE = 15;

    public function actionIndex($q = '')
    {
        $validator = new StringValidator();
        if (!$validator->validate($q)) {
            return $this->redirect(Url::home());
        }

        $search = Yii::$app->search;
        $searchData = $search->find($q); // Search by full iwndex.

        //$searchData = $search->find($q, ['model' => 'page']); // Search by index provided only by model `page`.

        $dataProvider = new ArrayDataProvider(
            [
                'allModels'  => $searchData['results'],
                'pagination' => ['pageSize' => self::PAGE_SIZE],
            ]
        );

        return $this->render(
            'index',
            [
                'hits'       => $dataProvider->getModels(),
                'pagination' => $dataProvider->getPagination(),
                'query'      => $searchData['query'],
                'total'      => count($searchData['results']),
            ]
        );
    }
}
