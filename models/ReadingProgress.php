<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $ID_reading_progress
 * @property int $ID_user
 * @property int $ID_manga
 * @property int $current_volume
 * @property string $status
 * @property string $update_at
 */
class ReadingProgress extends ActiveRecord
{
    public static function tableName()
    {
        return 'reading_progress';
    }

    public function rules()
    {
        return [
            [['ID_user', 'ID_manga'], 'required'],
            [['ID_user', 'ID_manga', 'current_volume'], 'integer'],
            [['current_volume'], 'default', 'value' => 0],
            [['status'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => 'Читаю'],
            [['update_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ID_reading_progress' => 'ID',
            'ID_user' => 'Пользователь',
            'ID_manga' => 'Манга',
            'current_volume' => 'Прочитано томов',
            'status' => 'Статус',
            'update_at' => 'Дата обновления',
        ];
    }

    public function getManga()
    {
        return $this->hasOne(Manga::class, ['ID_manga' => 'ID_manga']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['ID_user' => 'ID_user']);
    }
}
