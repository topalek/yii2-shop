<?php

namespace common\modules\page\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PageSearch represents the model behind the search form about `common\modules\page\models\Page`.
 */
class PageSearch extends Page
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [
                [
                    'title_uk',
                    'title_ru',
                    'title_en',
                    'content_uk',
                    'content_ru',
                    'content_en',
                    'updated_at',
                    'created_at',
                ],
                'safe',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Page::find();
        $query->joinWith('seo');

        if (!Yii::$app->request->get('sort')) {
            $query->orderBy('id DESC');
        }

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id'         => $this->id,
                'status'     => $this->status,
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
            ]
        );

        $query->andFilterWhere(['like', 'title_uk', $this->title_uk])
              ->andFilterWhere(['like', 'title_ru', $this->title_ru])
              ->andFilterWhere(['like', 'title_en', $this->title_en])
              ->andFilterWhere(['like', 'content_uk', $this->content_uk])
              ->andFilterWhere(['like', 'content_ru', $this->content_ru])
              ->andFilterWhere(['like', 'content_en', $this->content_en]);

        return $dataProvider;
    }
}
