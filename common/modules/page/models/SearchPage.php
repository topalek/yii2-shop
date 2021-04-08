<?php

namespace common\modules\page\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchPage represents the model behind the search form about `common\modules\page\models\Page`.
 */
class SearchPage extends Page
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['title', 'content', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Page::find();

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id'         => $this->id,
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
            ]
        );

        $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
