<?php

namespace common\modules\image\controllers;

use common\components\BaseController;
use common\modules\image\models\Image;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate($model_id, $model_name)
    {
        $model = new Image();
        $model->model_id = $model_id;
        $model->model_name = $model_name;
        $name = 'file';
        $model->imageFile = UploadedFile::getInstanceByName($name);
        if ($model->save()) {
            return Json::encode(
                [
                    'name'    => $model->image,
                    'id'      => $model->id,
                    'is_main' => $model->is_main === $model::MAIN_IMAGE,
                ]
            );
        } else {
            return Json::encode(
                [
                    'error' => $model->getFirstError('imageFile'),
                ]
            );
        }
    }

    public function actionDeleteImage($id)
    {
        if (\Yii::$app->request->isDelete) {
            if (Image::findOne($id)->delete()) {
                return Json::encode(['result' => true]);
            }
            return Json::encode(['result' => false]);
        } else {
            throw new NotFoundHttpException('Данная страница не существует.');
        }
    }

    public function actionSetAsMain($id)
    {
        if (\Yii::$app->request->isPost) {
            $model = Image::findOne($id);
            if ($model != null) {
                $model->setAsMain();
                return Json::encode(['result' => true]);
            }
            return Json::encode(['result' => false]);
        } else {
            throw new NotFoundHttpException('Данная страница не существует.');
        }
    }
}
