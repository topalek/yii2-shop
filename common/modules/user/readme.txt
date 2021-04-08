Щоб модуль працював в protected/config/web.php

 'modules' => [
 .....

     'user' => [
         'class' => 'common\modules\user\UserModule',
     ],

 .....
 ]

'components' => [
.....

    'user' => [
        'identityClass' => 'common\modules\user\models\User',
        'enableAutoLogin' => true,
    ],
    'authManager' => [
        'class' => 'common\modules\user\modules\rbac\components\PhpManager',
    ],

....
]

Запустити екшен для прописування ролей

public function actionSetRoles()
{
      $auth = Yii::$app->authManager;

      $auth->removeAll();

      $authorRule = new \common\modules\user\modules\rbac\rules\AuthorRule;
      $auth->add($authorRule);

      $notGuestRule = new \common\modules\user\modules\rbac\rules\NotGuestRule;
      $auth->add($notGuestRule);

      $adminAccessRule = new \common\modules\user\modules\rbac\rules\adminAccessRule;
      $auth->add($adminAccessRule);

      // add "createData" permission
      $createData = $auth->createPermission('createData');
      $createData->description = 'Create a data';
      $auth->add($createData);

      $readData = $auth->createPermission('readData');
      $readData->description = 'view news, comment, etc';
      $auth->add($readData);

      // add "updateData" permission
      $updateData = $auth->createPermission('updateData');
      $updateData->description = 'Update data';
      $auth->add($updateData);

      $deleteData = $auth->createPermission('deleteData');
      $deleteData->description = 'delete news, comment, etc';
      $auth->add($deleteData);

      $updateOwnData = $auth->createPermission('updateOwnData');
      $updateOwnData->description = 'update own data';
      $updateOwnData->ruleName = $authorRule->name;
      $auth->add($updateOwnData);

      $deleteOwnData = $auth->createPermission('deleteOwnData');
      $deleteOwnData->description = 'delete own data';
      $deleteOwnData->ruleName = $authorRule->name;
      $auth->add($deleteOwnData);

      $adminAccess = $auth->createPermission('adminAccess');
      $adminAccess->ruleName = $adminAccessRule->name;
      $adminAccess->description = 'manage users';
      $auth->add($adminAccess);

      $guest = $auth->createRole('guest');
      $auth->add($guest);
      $auth->addChild($guest, $readData);

      $user = $auth->createRole(User::ROLE_USER);
      $user->ruleName = $notGuestRule->name;
      $auth->add($user);
      $auth->addChild($user, $guest);
      $auth->addChild($user, $createData);
      $auth->addChild($user, $updateOwnData);
      $auth->addChild($user, $deleteOwnData);

      $admin = $auth->createRole(User::ROLE_ADMIN);
      $auth->add($admin);
      $auth->addChild($admin, $user);
      $auth->addChild($admin, $deleteData);
      $auth->addChild($admin, $adminAccess);
}

В екшені логіна прописати шлях до LoginModel
