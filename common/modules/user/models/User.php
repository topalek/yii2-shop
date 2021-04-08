<?php

namespace common\modules\user\models;

use common\components\BaseModel;
use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string  $username
 * @property string  $password_hash
 * @property string  $email
 * @property string  $role
 * @property integer $status
 * @property string  $auth_key
 * @property string  $access_token
 * @property string  $updated_at
 * @property string  $created_at
 * @property string  $deleted_at
 *
 */
class User extends BaseModel implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status'               => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     *
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne(
            [
                'verification_token' => $token,
                'status'             => self::STATUS_INACTIVE,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            ['password_hash', 'required', 'on' => ['create']],
            [['status'], 'integer'],
            ['email', 'email'],
            ['email', 'unique'],
            [['updated_at', 'created_at', 'deleted_at'], 'safe'],
            [['username', 'password_hash', 'role', 'auth_key', 'access_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'username'      => 'Логин',
            'password_hash' => 'Пароль',
            'email'         => 'E-mail',
            'role'          => 'Роль',
            'status'        => 'Status',
            'auth_key'      => 'Auth Key',
            'updated_at'    => 'Дата обновления',
            'created_at'    => 'Дата создания',
            'deleted_at'    => 'Дата удаления',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRole()
    {
        return self::getRoleArray()[$this->role];
    }

    public static function getRoleArray()
    {
        return [
            self::ROLE_USER  => 'Пользователь',
            self::ROLE_ADMIN => 'Админ',
        ];
    }

    public function getDisplayName()
    {
        return $this->username;
    }

    public function create()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->save(false);

            $auth = Yii::$app->authManager;
            $userRole = $auth->getRole(self::ROLE_USER);
            $auth->assign($userRole, $user->getId());

            return $user;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
