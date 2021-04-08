<?php

namespace common\modules\user\modules\rbac\rules;

use common\modules\user\models\User;
use yii\rbac\Rule;

class AdminAccessRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'adminAccess';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        $role = \Yii::$app->user->identity->role;
        if ($role == User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
