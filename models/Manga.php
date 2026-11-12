<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Модель для таблицы "manga".
 *
 * @property int $ID_manga
 * @property string $title
 * @property string $description
 * @property string $cover
 * @property string $type
 *
 * @property Volume $volume
 * @property Volume[] $volumes
 * @property ReadingProgress[] $readingProgresses
 */
class Manga extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @var int|null Виртуальное поле для редактирования количества томов
     */
    public $total_volumes_input;

    public static function tableName()
    {
        return 'manga';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'type'], 'required', 'message' => 'Поле "{attribute}" обязательно для заполнения.'],
            [['description'], 'string'],
            [['title', 'cover'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['total_volumes_input'], 'integer', 'min' => 1],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp, svg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ID_manga' => 'ID',
            'title' => 'Название тайтла',
            'description' => 'Описание',
            'cover' => 'Имя файла обложки или путь',
            'type' => 'Тип (Манга, Манхва и т.д.)',
            'imageFile' => 'Загрузить файл обложки',
            'total_volumes_input' => 'Количество томов всего',
        ];
    }

    public function getVolume()
    {
        return $this->hasOne(Volume::class, ['ID_manga' => 'ID_manga']);
    }

    public function getVolumes()
    {
        return $this->hasMany(Volume::class, ['ID_manga' => 'ID_manga']);
    }

    public function getReadingProgresses()
    {
        return $this->hasMany(ReadingProgress::class, ['ID_manga' => 'ID_manga']);
    }

    /**
     * Возвращает общее количество томов из таблицы volume
     * @return int
     */
    public function getTotalVolumes()
    {
        if ($this->volume) {
            return (int)$this->volume->volume_number;
        }
        return 0;
    }

    /**
     * Возвращает веб-URL обложки
     * @return string
     */
    public function getCoverUrl()
    {
        if (empty($this->cover)) {
            return Yii::getAlias('@web/images/logo.svg');
        }
        if (str_starts_with($this->cover, 'http://') || str_starts_with($this->cover, 'https://')) {
            return $this->cover;
        }
        $filename = basename(str_replace('\\', '/', $this->cover));
        return Yii::getAlias('@web/images/') . $filename;
    }

    /**
     * Получает прогресс чтения текущего или указанного пользователя
     * @param int|null $userId
     * @return ReadingProgress|null
     */
    public function getUserProgress($userId = null)
    {
        if ($userId === null) {
            if (Yii::$app->has('user') && !Yii::$app->user->isGuest) {
                $userId = Yii::$app->user->id;
            } else {
                $userId = 1;
            }
        }
        return ReadingProgress::findOne(['ID_manga' => $this->ID_manga, 'ID_user' => $userId]);
    }

    /**
     * Сохранение загруженной обложки
     * @return bool
     */
    public function uploadImage()
    {
        if ($this->imageFile) {
            $webImagesDir = Yii::getAlias('@webroot/images');
            if (!is_dir($webImagesDir)) {
                mkdir($webImagesDir, 0777, true);
            }
            $fileName = uniqid('manga_') . '.' . $this->imageFile->extension;
            $filePath = $webImagesDir . DIRECTORY_SEPARATOR . $fileName;
            if ($this->imageFile->saveAs($filePath)) {
                $this->cover = $fileName;
                return true;
            }
            return false;
        }
        return true;
    }
}
