<?php

namespace common\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\user\models\User;

/**
 * UserSearch represents the model behind the search form about `common\modules\user\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'password', 'email', 'role', 'auth_key', 'updated_at', 'created_at', 'deleted_at'], 'safe'],
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
        $query = User::find();

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
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
                'deleted_at' => $this->deleted_at,
            ]
        );

        $query->andFilterWhere(['like', 'username', $this->username])
              ->andFilterWhere(['like', 'password', $this->password])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'role', $this->role])
              ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        return $dataProvider;
    }
}
