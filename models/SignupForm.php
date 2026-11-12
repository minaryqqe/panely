<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            ['username', 'trim'],
            ['username', 'string', 'min' => 2, 'max' => 255, 'tooShort' => 'Логин должен содержать не менее 2 символов.'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Это имя пользователя уже занято.'],

            ['email', 'trim'],
            ['email', 'email', 'message' => 'Введите корректный адрес электронной почты.'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот email уже зарегистрирован.'],

            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен содержать не менее 6 символов.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя (логин)',
            'email' => 'Электронная почта (Email)',
            'password' => 'Пароль',
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->ID_role = 2; // Обычный пользователь по умолчанию

        if ($user->save()) {
            // Инициализируем записи прогресса чтения для нового пользователя
            $mangas = Manga::find()->all();
            foreach ($mangas as $manga) {
                $progress = new ReadingProgress();
                $progress->ID_user = $user->ID_user;
                $progress->ID_manga = $manga->ID_manga;
                $progress->current_volume = 0;
                $progress->status = 'Буду читать';
                $progress->save();
            }

            return $user;
        }

        // Если при сохранении в БД возникли ошибки, перенесем их в форму
        foreach ($user->errors as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->addError($attribute, $error);
            }
        }

        return null;
    }
}
