<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 23.05.14
 * Time: 10:46
 */

namespace common\components;

use common\modules\params\models\Params;
use common\modules\seo\models\Seo;
use common\modules\shop\models\Cart;
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
        Yii::$app->params = array_merge(Yii::$app->params, Params::getParamsList());

        Yii::$app->session->open();
        $this->view->params['cartItemCount'] = Cart::getItemsCount();
        $url = ltrim(BaseUrlManager::getUrlWithoutLangPrefix(), '/');

        if ($url == '') {
            $url = '/';
        }

        $seo = Seo::findByExternalLink($url);

        if ($seo != null) {
            $this->title = $seo->getMlTitle();
            $this->description = $seo->getMlContent();
            $this->keywords = $seo->getMlKeywords();
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
