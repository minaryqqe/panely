<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'ID_role'], 'required'],
            [['ID_role'], 'integer'],
            [['username', 'email', 'password'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ID_user' => 'ID',
            'username' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
            'ID_role' => 'Роль',
        ];
    }

    public function getRole()
    {
        return $this->hasOne(Role::class, ['ID_role' => 'ID_role']);
    }

    public function isAdmin()
    {
        return (int)$this->ID_role === 1;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['ID_user' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        return static::find()
            ->where(['username' => $username])
            ->orWhere(['email' => $username])
            ->one();
    }

    public function getId()
    {
        return $this->ID_user;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}