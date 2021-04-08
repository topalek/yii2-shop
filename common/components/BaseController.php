<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 23.05.14
 * Time: 10:46
 */

namespace common\components;

use app\modules\params\models\Params;
use app\modules\seo\models\Seo;
use Yii;
use yii\web\Controller;
use yii\web\Cookie;

class BaseController extends Controller
{

    public $title;
    public $description;
    public $keywords;
    public $noindex = false;
    public $nofollow = false;
    public $canonical;

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        Yii::$app->params = Params::getParamsList();

        Yii::$app->session->open();

        $url = ltrim(BaseUrlManager::getUrlWithoutLangPrefix(), '/');

        if ($url == '') {
            $url = '/';
        }

        $seo = Seo::findByExternalLink($url);

        if ($seo != null) {
            $this->title = $seo->mlTitle;
            $this->description = $seo->mlDescription;
            $this->keywords = $seo->mlKeywords;
        }

        Yii::$app->response->cookies->add(
            new Cookie(
                [
                    'name'  => 'sid',
                    'value' => Yii::$app->session->id,
                ]
            )
        );

        return parent::beforeAction($action);
    }
}
