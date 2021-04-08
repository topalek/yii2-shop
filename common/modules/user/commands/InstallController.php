<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 18.05.15
 * Time: 10:45
 */

namespace common\modules\user\commands;


use common\modules\user\models\User;
use common\modules\user\modules\rbac\rules\AdminAccessRule;
use common\modules\user\modules\rbac\rules\AuthorRule;
use common\modules\user\modules\rbac\rules\NotGuestRule;
use Yii;
use yii\console\Controller;

class InstallController extends Controller
{

}
