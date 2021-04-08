<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 03.10.14
 * Time: 10:59
 */

namespace common\modules\user\modules\rbac\components;


use Yii;
use yii\rbac\Role;

class PhpManager extends \yii\rbac\PhpManager
{
    public $itemFile = '@app/modules/user/modules/rbac/data/item.php';
    public $assignmentFile = '@app/modules/user/modules/rbac/data/assignmentFile.php';
    public $ruleFile = '@app/modules/user/modules/rbac/data/rule.php';

    public function init()
    {
        if (!is_file(Yii::getAlias($this->itemFile))) {
            file_put_contents(Yii::getAlias($this->itemFile), '<?php return [];?>');
        }

        if (!is_file(Yii::getAlias($this->assignmentFile))) {
            file_put_contents(Yii::getAlias($this->assignmentFile), '<?php return [];?>');
        }

        if (!is_file(Yii::getAlias($this->ruleFile))) {
            file_put_contents(Yii::getAlias($this->ruleFile), '<?php return [];?>');
        }

        parent::init();

        if (@isset(Yii::$app->user)) {
            $user = Yii::$app->getUser();
            if (!$user->isGuest) {
                $identity = $user->getIdentity();
                if (!$this->getAssignment($identity->role, $identity->getId())) {
                    $role = new Role(
                        [
                            'name' => $identity->role,
                        ]
                    );
                    $this->assign($role, $identity->getId());
                }
                $this->removeOldAssignment($identity);
            }
        }
    }

    protected function removeOldAssignment($identity)
    {
        $assignments = $this->getAssignments($identity->getId());
        foreach ($assignments as $assign) {
            if ($assign->roleName !== $identity->role) {
                $this->revoke($this->getRole($assign->roleName), $identity->getId());
            }
        }
    }
}
