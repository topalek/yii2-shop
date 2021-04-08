<?php

namespace common\modules\image\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ImageSearch represents the model behind the search form about `common\modules\image\models\Image`.
 */
class ImageSearch extends Image
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'model_id', 'is_main'], 'integer'],
            [['model_name', 'image'], 'safe'],
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
        $query = Image::find();

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
                'id'       => $this->id,
                'model_id' => $this->model_id,
                'is_main'  => $this->is_main,
            ]
        );

        $query->andFilterWhere(['like', 'model_name', $this->model_name])
              ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
