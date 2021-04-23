<?php

namespace common\modules\catalog\models;

use Yii;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();
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
                'category_id' => $this->category_id,
            ]
        );

//        $query->andFilterWhere(['like', 'title_uk', $this->title_uk])
//            ->andFilterWhere(['like', 'title_ru', $this->title_ru])
//            ->andFilterWhere(['like', 'title_en', $this->title_en])
//            ->andFilterWhere(['like', 'content_uk', $this->content_uk])
//            ->andFilterWhere(['like', 'content_ru', $this->content_ru])
//            ->andFilterWhere(['like', 'content_en', $this->content_en]);

        return $dataProvider;
    }
}
