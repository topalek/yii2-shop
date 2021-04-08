<?php

namespace common\modules\htmlBlock\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HtmlBlockSearch represents the model behind the search form about `common\modules\htmlBlock\models\HtmlBlock`.
 */
class HtmlBlockSearch extends HtmlBlock
{
    public function rules()
    {
        return [
            [['id', 'status', 'ordering'], 'integer'],
            [['title', 'position', 'content', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = HtmlBlock::find();

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
                'status'     => $this->status,
                'ordering'   => $this->ordering,
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
            ]
        );

        $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'position', $this->position])
              ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
