<?php

namespace common\modules\params\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParamsSearch represents the model behind the search form about `common\modules\params\models\Params`.
 */
class ParamsSearch extends Params
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'sys_name', 'value'], 'safe'],
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
        $query = Params::find();

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
                'id'     => $this->id,
                'status' => $this->status,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'sys_name', $this->sys_name])
              ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
