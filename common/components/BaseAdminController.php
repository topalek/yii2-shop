<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 23.05.14
 * Time: 10:46
 */

namespace common\components;

use common\modules\params\models\Params;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BaseAdminController extends Controller
{
    public $layout = '@backend/views/layouts/main';

    public function actions()
    {
        return [
            'error' => [
                'class'  => 'yii\web\ErrorAction',
                'layout' => 'main-login',
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->params = array_merge(Yii::$app->params, Params::getParamsList());
        Yii::$app->language = 'ru';
        Yii::$app->sourceLanguage = 'ru-RU';

        return parent::beforeAction($action);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'request-password-reset', 'reset-password', 'error'],
                        'allow'   => true,
                        'roles'   => ['?'],
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
     * @param $table
     *
     * @return bool|string
     */
    public function getMaxOrder($table)
    {
        $maxOrder = (new Query())
            ->select('MAX(ordering) as maxOrder')
            ->from($table)
            ->scalar();
        return $maxOrder;
    }

    public function actionImperaviUpload($model_name, $model_id)
    {
        $pic = UploadedFile::getInstanceByName('file');
        $model_name = strtolower($model_name);
        if ($pic->type == 'image/png' || $pic->type == 'image/jpg' || $pic->type == 'image/gif' || $pic->type == 'image/jpeg' || $pic->type == 'image/pjpeg') {
            $path = str_replace('backend', 'frontend', Yii::$app->basePath) . "/web/uploads/$model_name/$model_id/";
            $imperavi_path = $path . 'imperavi/';
            $name = md5(time()) . '.jpg';

            if (!is_dir($path)) {
                FileHelper::createDirectory($path, 0777);
            }
            if (!is_dir($imperavi_path)) {
                FileHelper::createDirectory($imperavi_path, 0777);
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($pic->saveAs($imperavi_path . $name)) {
                return ['filelink' => "/uploads/$model_name/$model_id/imperavi/" . $name];
            } else {
                return ['error' => true];
            }
        } else {
            return false;
        }
    }

    public function actionImagesGet($model_name, $model_id)
    {
        $options = ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif', '*.ico']];
        $model_name = strtolower($model_name);
        $path = str_replace('backend', 'frontend', Yii::$app->basePath) . "/web/uploads/$model_name/$model_id/";
        $url = Yii::$app->params['frontendUrl'] . "/uploads/$model_name/$model_id/imperavi/";
        $imperavi_path = $path . 'imperavi';
        Yii::$app->response->format = Response::FORMAT_JSON;
        $files = [];

        foreach (FileHelper::findFiles($imperavi_path, $options) as $path) {
            $file = basename($path);
            $files[] = [
                'id'    => $file,
                'title' => $file,
                'thumb' => $url . $file,
                'image' => $url . $file,
            ];
        }

        return $files;
    }

    public function actionDeleteImperaviImg()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $imgUrl = Yii::$app->request->post('imgUrl');
            if ($imgUrl) {
                // $imgFile = str_replace('backend', 'frontend', Yii::$app->basePath) . '/web' . $imgUrl;
                // print_r($imgFile);
                @unlink($imgUrl);
                return true;
            }
        } else {
            throw new NotFoundHttpException('?????????????? ???? ????????????????????.');
        }
    }

    public function actionDeleteImg($id, $model_name)
    {
        $result = false;
        $model_name = urldecode($model_name);
        include_once '../../' . $model_name . '.php';
        $class = Yii::createObject($model_name);
        $model = $class::findOne($id);
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

    /**
     * @return array|bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionSwitchState()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $class ActiveRecord */
        $class = Yii::$app->request->get('class');
        $fieldName = Yii::$app->request->get('fieldName');
        $model = $class::findOne(Yii::$app->request->get('id'));

        if ($model->$fieldName == 1) {
            $model->$fieldName = 0;
        } else {
            $model->$fieldName = 1;
        }

        if ($model->update(false, [$fieldName])) {
            return true;
        } else {
            return ['message' => '?????????????????? ????????????'];
        }
    }

    public function actionTest()
    {
        return __CLASS__;
    }
}
