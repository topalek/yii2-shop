<?php

namespace common\modules\translate\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\translate\models\Message;

/**
 * TranslateSearch represents the model behind the search form about `common\modules\translate\models\Translate`.
 */
class TranslateSearch extends Translate
{
    /**
     * @inheritdoc
     */
    public $category, $message;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['language', 'translation', 'category', 'message'], 'safe'],
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
        $query = Translate::find();
        $query->joinWith('source');
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
                'translate.id' => $this->id,
            ]
        );

        $query->andFilterWhere(['translate.language' => $this->language])
              ->andFilterWhere(['source_translate.category' => $this->category]);
        if (Yii::$app->request->get('TranslateSearch')) {
            $translate_param = Yii::$app->request->get('TranslateSearch')['translation'];
            if ($translate_param != null) {
                if ($translate_param == true) {
                    $query->andWhere(['is not', 'translate.translation', null]);
                } else {
                    $query->andWhere(['is', 'translate.translation', null]);
                }
            }
        }
        $query->andFilterWhere(['like', 'source_translate.message', $this->message]);
        return $dataProvider;
    }
}
