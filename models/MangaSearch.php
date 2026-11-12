<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class MangaSearch extends Manga
{
    /**
     * @var string Строка для общего поиска по названию или описанию
     */
    public $searchQuery;

    public function rules()
    {
        return [
            [['ID_manga'], 'integer'],
            [['title', 'type', 'description', 'searchQuery'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Создает ActiveDataProvider с учетом переданных фильтров
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Manga::find()->with(['volume']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => ['ID_manga' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Полнотекстовый поиск по searchQuery
        if (!empty($this->searchQuery)) {
            $query->andFilterWhere([
                'or',
                ['like', 'title', $this->searchQuery],
                ['like', 'description', $this->searchQuery],
            ]);
        }

        // Фильтры по колонкам
        $query->andFilterWhere(['ID_manga' => $this->ID_manga]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['type' => $this->type]);

        return $dataProvider;
    }
}
