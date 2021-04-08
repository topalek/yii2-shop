<?php

namespace common\modules\translate\controllers;

use common\components\BaseAdminController;
use common\modules\translate\models\Translate;
use common\modules\translate\models\TranslateSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AdminMessageController implements the CRUD actions for Message model.
 */
class AdminTranslateController extends BaseAdminController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
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
     * Lists all Message models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TranslateSearch();
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
     * Displays a single Message model.
     *
     * @param integer $id
     * @param string  $language
     *
     * @return mixed
     */
    public function actionView($id, $language)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id, $language),
            ]
        );
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @param string  $language
     *
     * @return Translate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $language)
    {
        if (($model = Translate::findOne(['id' => $id, 'language' => $language])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('site', 'Сторінка не існує'));
        }
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Translate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('humane', 'Сохранено');
            return $this->redirect(['update', 'id' => $model->id, 'language' => $model->language]);
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
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @param string  $language
     *
     * @return mixed
     */
    public function actionUpdate($id, $language)
    {
        $model = $this->findModel($id, $language);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('humane', 'Сохранено');
            return $this->redirect(['update', 'id' => $model->id, 'language' => $model->language]);
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
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @param string  $language
     *
     * @return mixed
     */
    public function actionDelete($id, $language)
    {
        $this->findModel($id, $language)->delete();

        return $this->redirect(['index']);
    }

    public function actionUpdateData()
    {
        $cmd = PHP_BINDIR . '/php core/yii message core/config/translate_conf.php';

        $data = false;
        $fp = popen($cmd . '', 'r');
        while (!feof($fp)) {
            $buffer = fgets($fp, 4096);

            if ($buffer != false) {
                $data .= $buffer;
            } else {
                break;
            }
        }

        pclose($fp);

        return $this->redirect(['/translate/admin-translate']);
    }
}
