<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Manga;
use app\models\Volume;
use app\models\ReadingProgress;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'exit', 'inc-progress'],
                'rules' => [
                    [
                        'actions' => ['logout', 'exit', 'inc-progress'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'exit' => ['get', 'post'],
                    'inc-progress' => ['get', 'post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Главная страница с выводом статистики, прогресса и тайтлов из БД
     */
    public function actionIndex()
    {
        $userId = !Yii::$app->user->isGuest ? Yii::$app->user->id : 1;

        // Статистика из БД
        $statInProgress = ReadingProgress::find()->where(['ID_user' => $userId, 'status' => 'Читаю'])->count();
        $statReadVolumes = (int)ReadingProgress::find()->where(['ID_user' => $userId])->sum('current_volume');
        $statWishlist = (new \yii\db\Query())->from('wishlist')->where(['ID_user' => $userId])->count();

        // Все тайтлы с томами
        $mangas = Manga::find()->with(['volume'])->all();

        // Продолжить чтение из БД
        $continueProgress = ReadingProgress::find()
            ->where(['ID_user' => $userId, 'status' => 'Читаю'])
            ->with(['manga', 'manga.volume'])
            ->limit(3)
            ->all();

        return $this->render('index', [
            'mangas' => $mangas,
            'statInProgress' => $statInProgress ?: count($mangas),
            'statReadVolumes' => $statReadVolumes ?: (int)Volume::find()->sum('volume_number'),
            'statWishlist' => $statWishlist ?: 17,
            'continueProgress' => $continueProgress,
        ]);
    }

    /**
     * Страница прогресса чтения из БД с фильтрацией по статусу, типу и названию
     */
    public function actionProgress()
    {
        $userId = !Yii::$app->user->isGuest ? Yii::$app->user->id : 1;
        $status = Yii::$app->request->get('status', 'all');
        $type = Yii::$app->request->get('type', 'all');
        $q = Yii::$app->request->get('q', '');

        $statusMap = [
            'reading' => 'Читаю',
            'done' => 'Прочитано',
            'paused' => 'Отложено',
            'planned' => 'Буду читать',
            'Читаю' => 'Читаю',
            'Прочитано' => 'Прочитано',
            'Отложено' => 'Отложено',
            'Буду читать' => 'Буду читать',
            'читаю' => 'Читаю',
            'прочитано' => 'Прочитано',
            'отложено' => 'Отложено',
            'буду читать' => 'Буду читать',
        ];

        $query = ReadingProgress::find()
            ->where(['reading_progress.ID_user' => $userId])
            ->joinWith(['manga', 'manga.volume']);

        $normalizedStatus = null;
        if (!empty($status) && $status !== 'all') {
            $normalizedStatus = $statusMap[$status] ?? $status;
            $query->andWhere(['reading_progress.status' => $normalizedStatus]);
        }

        if (!empty($type) && $type !== 'all') {
            $query->andWhere(['manga.type' => $type]);
        }

        if (!empty($q)) {
            $query->andWhere(['like', 'manga.title', $q]);
        }

        $progressList = $query->all();

        return $this->render('progress', [
            'progressList' => $progressList,
            'currentStatus' => $normalizedStatus ?: 'all',
            'currentType' => $type,
            'currentQ' => $q,
        ]);
    }

    /**
     * Поиск и фильтрация манги из БД по статусу, типу и названию
     */
    public function actionSearch()
    {
        $userId = !Yii::$app->user->isGuest ? Yii::$app->user->id : 1;
        $q = Yii::$app->request->get('q', '');
        $types = Yii::$app->request->get('type', []);
        $statuses = Yii::$app->request->get('status', []);

        $statusMap = [
            'reading' => 'Читаю',
            'done' => 'Прочитано',
            'paused' => 'Отложено',
            'planned' => 'Буду читать',
            'Читаю' => 'Читаю',
            'Прочитано' => 'Прочитано',
            'Отложено' => 'Отложено',
            'Буду читать' => 'Буду читать',
            'читаю' => 'Читаю',
            'прочитано' => 'Прочитано',
            'отложено' => 'Отложено',
            'буду читать' => 'Буду читать',
        ];

        $normalizedStatuses = [];
        if (!empty($statuses)) {
            foreach ((array)$statuses as $st) {
                if ($st !== 'all' && isset($statusMap[$st])) {
                    $normalizedStatuses[] = $statusMap[$st];
                }
            }
        }

        $query = Manga::find()->with(['volume']);

        if (!empty($q)) {
            $query->andWhere([
                'or',
                ['like', 'title', $q],
                ['like', 'description', $q],
            ]);
        }

        if (!empty($types)) {
            $query->andFilterWhere(['type' => (array)$types]);
        }

        if (!empty($normalizedStatuses)) {
            $query->innerJoin('reading_progress', 'reading_progress.ID_manga = manga.ID_manga')
                  ->andWhere(['reading_progress.ID_user' => $userId])
                  ->andWhere(['reading_progress.status' => $normalizedStatuses]);
        }

        $mangas = $query->all();

        return $this->render('search', [
            'mangas' => $mangas,
            'q' => $q,
            'selectedTypes' => (array)$types,
            'selectedStatuses' => $normalizedStatuses,
        ]);
    }

    /**
     * Страница отдельного тайтла манги из БД
     */
    public function actionManga($id = null)
    {
        if ($id === null) {
            $manga = Manga::find()->with(['volume'])->one();
        } else {
            $manga = Manga::find()->where(['ID_manga' => $id])->with(['volume'])->one();
        }

        if (!$manga) {
            throw new NotFoundHttpException('Тайтл не найден.');
        }

        // Обновление статуса чтения текущего пользователя
        if (Yii::$app->request->isPost && !Yii::$app->user->isGuest) {
            $status = Yii::$app->request->post('status');
            if ($status) {
                $progress = ReadingProgress::findOne([
                    'ID_manga' => $manga->ID_manga,
                    'ID_user' => Yii::$app->user->id,
                ]);
                if (!$progress) {
                    $progress = new ReadingProgress();
                    $progress->ID_user = Yii::$app->user->id;
                    $progress->ID_manga = $manga->ID_manga;
                    $progress->current_volume = 0;
                }
                $progress->status = $status;
                $progress->save();
                Yii::$app->session->setFlash('success', 'Статус чтения обновлен!');
                return $this->refresh();
            }
        }

        $userId = !Yii::$app->user->isGuest ? Yii::$app->user->id : 1;
        $progress = ReadingProgress::findOne([
            'ID_manga' => $manga->ID_manga,
            'ID_user' => $userId,
        ]);

        return $this->render('manga', [
            'manga' => $manga,
            'progress' => $progress,
        ]);
    }

    /**
     * Увеличение прогресса прочитанных томов (+1 том)
     */
    public function actionIncProgress($id)
    {
        $userId = !Yii::$app->user->isGuest ? Yii::$app->user->id : 1;
        $manga = Manga::findOne($id);

        if (!$manga) {
            throw new NotFoundHttpException('Тайтл не найден.');
        }

        $progress = ReadingProgress::findOne(['ID_manga' => $id, 'ID_user' => $userId]);
        if (!$progress) {
            $progress = new ReadingProgress();
            $progress->ID_user = $userId;
            $progress->ID_manga = $id;
            $progress->current_volume = 0;
            $progress->status = 'Читаю';
        }

        $total = $manga->totalVolumes;
        if ($total == 0 || $progress->current_volume < $total) {
            $progress->current_volume++;
            if ($total > 0 && $progress->current_volume >= $total) {
                $progress->status = 'Прочитано';
            }
            $progress->save();
        }

        if (Yii::$app->request->isAjax) {
            $pct = $total > 0 ? round(($progress->current_volume / $total) * 100) : 0;
            return $this->asJson([
                'success' => true,
                'current_volume' => $progress->current_volume,
                'total_volumes' => $total,
                'status' => $progress->status,
                'percent' => $pct,
            ]);
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['site/index']);
    }

    /**
     * Профиль пользователя с информацией и коллекцией из БД
     */
    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            // Если гость, покажем пользователя по умолчанию для демонстрации
            $user = \app\models\User::findOne(1);
        } else {
            $user = Yii::$app->user->identity;
        }

        $nowReading = ReadingProgress::find()
            ->where(['ID_user' => $user->ID_user, 'status' => 'Читаю'])
            ->with(['manga', 'manga.volume'])
            ->all();

        $collection = ReadingProgress::find()
            ->where(['ID_user' => $user->ID_user])
            ->with(['manga', 'manga.volume'])
            ->all();

        return $this->render('profile', [
            'user' => $user,
            'nowReading' => $nowReading,
            'collection' => $collection,
        ]);
    }

    /**
     * Вход в систему
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Регистрация нового пользователя
     */
    public function actionRegister()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            if (Yii::$app->user->login($user)) {
                return $this->goHome();
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Выход из системы
     */
    public function actionExit()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Контактная форма
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * О проекте
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
