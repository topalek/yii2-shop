<?php

namespace frontend\controllers;

use common\components\BaseController;
use common\modules\catalog\models\Category;
use common\modules\catalog\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $categories = Category::find()->limit(4)->all();
        $products = Product::find()->active()->popular()->limit(8)->all();
        if (count($products) < 8) {
            $products = Product::find()->active()->limit(8)->all();
        }

        return $this->render('index', compact('products', 'categories'));
    }

    public function actionSearch()
    {
        $q = Yii::$app->request->get('q');
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => Product::find()->with('seo')->where(['like', 'title_ru', $q])->orWhere(
                    ['like', 'title_uk', $q]
                )->orWhere(
                    ['like', 'description_ru', $q]
                )->orWhere(
                    ['like', 'description_uk', $q]
                ),
                'pagination' => [
                    'pageSize' => 12,
                ],
            ]
        );
        return $this->render('search', ['provider' => $dataProvider, 'q' => $q]);
    }
}
