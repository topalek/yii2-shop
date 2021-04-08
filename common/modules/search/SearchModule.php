<?php

namespace common\modules\search;

use yii\base\Module;

class SearchModule extends Module
{
    public $controllerNamespace = 'common\modules\search\controllers';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    //    public function bootstrap($app)
    //    {
    //        if ($app instanceof Application) {
    //            $app->controllerMap[$this->id] = [
    //                'class' => 'common\modules\search\commands\SearchController',
    //                'module' => $this,
    //            ];
    //        }
    //    }
}
