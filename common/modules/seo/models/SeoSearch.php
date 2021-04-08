<?php

namespace common\modules\seo\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SeoSearch represents the model behind the search form about `common\modules\seo\models\Seo`.
 */
class SeoSearch extends Seo
{
    public function rules()
    {
        return [
            [['id', 'noindex', 'nofollow', 'in_sitemap', 'is_canonical', 'model_id', 'status'], 'integer'],
            [
                [
                    'title_ru',
                    'description_ru',
                    'keywords_ru',
                    'head_block',
                    'external_link',
                    'internal_link',
                    'external_link_with_cat',
                    'model_name',
                    'updated_at',
                ],
                'safe',
            ],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Seo::find();

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
                'id'           => $this->id,
                'noindex'      => $this->noindex,
                'nofollow'     => $this->nofollow,
                'in_sitemap'   => $this->in_sitemap,
                'is_canonical' => $this->is_canonical,
                'model_id'     => $this->model_id,
                'status'       => $this->status,
                'updated_at'   => $this->updated_at,
            ]
        );

        $query->andFilterWhere(['like', 'title_uk', $this->title_uk])
              ->andFilterWhere(['like', 'description_uk', $this->description_uk])
              ->andFilterWhere(['like', 'keywords_uk', $this->keywords_uk])
              ->andFilterWhere(['like', 'head_block', $this->head_block])
              ->andFilterWhere(['like', 'external_link', $this->external_link])
              ->andFilterWhere(['like', 'internal_link', $this->internal_link])
              ->andFilterWhere(['like', 'external_link_with_cat', $this->external_link_with_cat])
              ->andFilterWhere(['like', 'model_name', $this->model_name]);

        return $dataProvider;
    }
}
