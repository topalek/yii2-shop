<?php

namespace common\modules\translate\models;

use common\modules\translate\models\SourceMessage;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SourceTranslateSearch represents the model behind the search form about `common\modules\translate\models\SourceTranslate`.
 */
class SourceTranslateSearch extends SourceTranslate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['category', 'message'], 'safe'],
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
        $query = SourceTranslate::find();

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
                'id' => $this->id,
            ]
        );

        $query->andFilterWhere(['like', 'category', $this->category])
              ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
