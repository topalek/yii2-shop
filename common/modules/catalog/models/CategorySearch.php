<?php

namespace common\modules\catalog\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategorySearch represents the model behind the search form about `common\modules\catalog\models\Category`.
 */
class CategorySearch extends CatalogCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tree', 'lft', 'rgt', 'depth'], 'integer'],
            [
                [
                    'title_uk',
                    'title_ru',
                    'title_en',
                    'description_uk',
                    'description_ru',
                    'description_en',
                    'main_img',
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
        $query = CatalogCategory::find();

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
                'tree'       => $this->tree,
                'lft'        => $this->lft,
                'rgt'        => $this->rgt,
                'depth'      => $this->depth,
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
            ]
        );

        $query->andFilterWhere(['like', 'title_uk', $this->title_uk])
              ->andFilterWhere(['like', 'title_ru', $this->title_ru])
              ->andFilterWhere(['like', 'title_en', $this->title_en])
              ->andFilterWhere(['like', 'description_uk', $this->description_uk])
              ->andFilterWhere(['like', 'description_ru', $this->description_ru])
              ->andFilterWhere(['like', 'description_en', $this->description_en])
              ->andFilterWhere(['like', 'main_img', $this->main_img]);

        return $dataProvider;
    }

    public function adminSearch($params)
    {
        $query = CatalogCategory::find();
        $query->where('depth=0');
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

        $query->andFilterWhere(['like', 'title_ru', $this->title_uk])
              ->andFilterWhere(['like', 'description_ru', $this->description_uk]);

        return $dataProvider;
    }
}
