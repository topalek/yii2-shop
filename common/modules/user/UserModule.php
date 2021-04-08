<?php

namespace common\modules\user;

use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\console\Application;

class UserModule extends Module implements BootstrapInterface
{
    public $controllerNamespace = 'common\modules\user\controllers';

    public function init()
    {
        parent::init();
    }

    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $this->controllerNamespace = 'common\modules\user\commands';
        }
    }
}
