<?php

namespace app\models;

use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    public static function tableName()
    {
        return 'role';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 25],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ID_role' => 'ID',
            'name' => 'Название роли',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['ID_role' => 'ID_role']);
    }
}
