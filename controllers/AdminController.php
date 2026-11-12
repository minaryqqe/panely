<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\Manga;
use app\models\MangaSearch;
use app\models\Volume;
use app\models\ReadingProgress;
use app\models\User;
use app\models\Role;

class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    }
                    throw new ForbiddenHttpException('Доступ в панель администратора ограничен. У вас нет прав администратора.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-user' => ['post'],
                    'change-role' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Панель администратора: дашборд со списком всей манги и статистикой
     */
    public function actionIndex()
    {
        $searchModel = new MangaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        // Общая статистика
        $totalManga = Manga::find()->count();
        $totalUsers = User::find()->count();
        $totalVolumes = (int)Volume::find()->sum('volume_number');
        $totalProgress = ReadingProgress::find()->count();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalManga' => $totalManga,
            'totalUsers' => $totalUsers,
            'totalVolumes' => $totalVolumes,
            'totalProgress' => $totalProgress,
        ]);
    }

    /**
     * Добавление новой манги в БД
     */
    public function actionCreate()
    {
        $model = new Manga();
        $model->type = 'Манга';
        $model->total_volumes_input = 1;

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $model->uploadImage();
            }

            if (empty($model->cover)) {
                $model->cover = 'logo.svg';
            }

            if ($model->save()) {
                // Создаем или обновляем запись в таблице volume
                $volume = new Volume();
                $volume->ID_manga = $model->ID_manga;
                $volume->volume_number = (int)$model->total_volumes_input ?: 1;
                $volume->save();

                Yii::$app->session->setFlash('success', "Тайтл «{$model->title}» успешно добавлен в БД!");
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование манги в БД
     */
    public function actionUpdate($id)
    {
        $model = Manga::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Тайтл не найден.');
        }

        $volume = Volume::findOne(['ID_manga' => $model->ID_manga]);
        if (!$volume) {
            $volume = new Volume();
            $volume->ID_manga = $model->ID_manga;
            $volume->volume_number = 1;
        }

        $model->total_volumes_input = $volume->volume_number;

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $model->uploadImage();
            }

            if ($model->save()) {
                $volume->volume_number = (int)$model->total_volumes_input ?: 1;
                $volume->save();

                Yii::$app->session->setFlash('success', "Тайтл «{$model->title}» успешно обновлен!");
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'volume' => $volume,
        ]);
    }

    /**
     * Удаление манги из БД
     */
    public function actionDelete($id)
    {
        $model = Manga::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Тайтл не найден.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Удаляем связанные записи из зависимых таблиц
            ReadingProgress::deleteAll(['ID_manga' => $id]);
            Yii::$app->db->createCommand()->delete('progress', ['ID_manga' => $id])->execute();
            Yii::$app->db->createCommand()->delete('wishlist', ['ID_manga' => $id])->execute();
            Volume::deleteAll(['ID_manga' => $id]);

            $title = $model->title;
            $model->delete();

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Тайтл «{$title}» успешно удален из БД.");
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ошибка при удалении: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Управление пользователями
     */
    public function actionUsers()
    {
        $users = User::find()->with(['role'])->all();
        $roles = Role::find()->all();

        return $this->render('users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Смена роли пользователя
     */
    public function actionChangeRole($id, $role_id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        // Защита: нельзя снять права с самого себя
        if ($user->ID_user === Yii::$app->user->id && (int)$role_id !== 1) {
            Yii::$app->session->setFlash('error', 'Вы не можете отозвать права администратора у самого себя!');
            return $this->redirect(['users']);
        }

        $user->ID_role = (int)$role_id;
        if ($user->save(false)) {
            Yii::$app->session->setFlash('success', "Роль пользователя {$user->username} успешно изменена.");
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось обновить роль пользователя.');
        }

        return $this->redirect(['users']);
    }

    /**
     * Удаление пользователя
     */
    public function actionDeleteUser($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        if ($user->ID_user === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Вы не можете удалить свою собственную учетную запись!');
            return $this->redirect(['users']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            ReadingProgress::deleteAll(['ID_user' => $id]);
            Yii::$app->db->createCommand()->delete('progress', ['ID_user' => $id])->execute();
            Yii::$app->db->createCommand()->delete('wishlist', ['ID_user' => $id])->execute();
            $username = $user->username;
            $user->delete();

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Пользователь {$username} успешно удален.");
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ошибка при удалении пользователя: ' . $e->getMessage());
        }

        return $this->redirect(['users']);
    }
}
