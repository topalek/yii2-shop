<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 23.05.14
 * Time: 10:46
 */

namespace common\components;

use app\modules\params\models\Params;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BaseAdminController extends Controller
{
    public $layout = '@backend/views/layouts/main';

    public function beforeAction($action)
    {
//        Yii::$app->params = Params::getParamsList();
        Yii::$app->language = 'ru';
        Yii::$app->sourceLanguage = 'ru-RU';

        return parent::beforeAction($action);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        /*return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['adminAccess'],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];*/
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'forgot-password'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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

    public function actionImperaviUpload($model_name)
    {
        $pic = UploadedFile::getInstanceByName('file');
        $model_name = strtolower($model_name);
        if ($pic->type == 'image/png' || $pic->type == 'image/jpg' || $pic->type == 'image/gif' || $pic->type == 'image/jpeg' || $pic->type == 'image/pjpeg') {
            $path = Yii::$app->basePath . "/../uploads/$model_name/";
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
                return ['filelink' => "/uploads/$model_name/imperavi/" . $name];
            } else {
                return ['error' => true];
            }
        } else {
            return false;
        }
    }

    public function actionDeleteImperaviImg()
    {
        if (Yii::$app->request->isAjax) {
            $imgUrl = Yii::$app->request->post('imgUrl');
            if ($imgUrl) {
                @unlink(Yii::$app->basePath . '/..' . $imgUrl);
            }
        } else {
            throw new NotFoundHttpException('Станица не существует.');
        }
    }
}
