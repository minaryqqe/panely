<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $ID_volume
 * @property int $ID_manga
 * @property int $volume_number
 */
class Volume extends ActiveRecord
{
    public static function tableName()
    {
        return 'volume';
    }

    public function rules()
    {
        return [
            [['ID_manga', 'volume_number'], 'required'],
            [['ID_manga', 'volume_number'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ID_volume' => 'ID тома',
            'ID_manga' => 'ID манги',
            'volume_number' => 'Количество томов',
        ];
    }

    public function getManga()
    {
        return $this->hasOne(Manga::class, ['ID_manga' => 'ID_manga']);
    }
}
