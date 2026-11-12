<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Manga;
use app\models\MangaSearch;
use app\models\ReadingProgress;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class MangaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['my-list', 'mylist'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Главный каталог с поддержкой поиска и фильтров
     */
    public function actionIndex()
    {
        $searchModel = new MangaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Получаем динамический список уникальных типов (Манга, Манхва и т.д.) для выпадающего списка
        $types = Manga::find()->select('type')->distinct()->column();
        $typesList = !empty($types) ? array_combine($types, $types) : [];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mangas' => $dataProvider->getModels(),
            'typesList' => $typesList,
        ]);
    }

    public function actionView($id)
    {
        $manga = Manga::findOne($id);
        if (!$manga) {
            throw new NotFoundHttpException('Манга не найдена.');
        }

        return $this->render('view', [
            'manga' => $manga,
        ]);
    }

    public function actionMyList()
    {
        $userId = Yii::$app->user->id;
        $progressList = ReadingProgress::find()
            ->where(['ID_user' => $userId])
            ->with(['manga', 'manga.volume'])
            ->all();

        return $this->render('mylist', [
            'progressList' => $progressList,
        ]);
    }
}
