<?php

namespace common\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

/**
 * ProductSearch represents the model behind the search form about `common\modules\catalog\models\Product`.
 *
 * @property string $original [varchar(255)]
 */
class ProductSearch extends Product
{
    public $propertyIds, $sort, $pager;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status'], 'integer'],
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
            [['price'], 'number'],
            ['propertyIds', 'safe'],
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
    public function adminSearch($params)
    {
        $query = Product::find();

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
                'price'       => $this->price,
                'category_id' => $this->category_id,
                'status'      => $this->status,
                'updated_at'  => $this->updated_at,
                'created_at'  => $this->created_at,
            ]
        );

        $query->andFilterWhere(['like', 'title_ru', $this->title_ru]);

        return $dataProvider;
    }


    public function search($params)
    {
        $viewType = Yii::$app->session->get('viewType', 'block');
        $lang = Yii::$app->language;
        $this->sort = new Sort(
            [
                'attributes' => [
                    'title'      => [
                        'asc'     => ['title_' . $lang => SORT_ASC],
                        'desc'    => ['title_' . $lang => SORT_DESC],
                        'default' => SORT_ASC,
                        'label'   => Yii::t('catalog', 'Название'),
                    ],
                    'price_from' => [
                        'asc'     => ['price' => SORT_ASC],
                        'desc'    => ['price' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label'   => Yii::t('catalog', 'Цена'),
                    ],
                ],
                'route'      => $this->category->getSeoUrl(),
                'params'     => [],
            ]
        );

        $query = Product::find();
        $query->with(['seo']);
        $query->leftJoin('product_property', 'product_property.product_id=product.id');
        $query->groupBy('product.id');
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'pagination' => [
                    'pageSize' => ($viewType == Product::VIEW_TYPE_BLOCK) ? 12 : 8,
                    'route'    => $this->category->getSeoUrl(),
                    'params'   => [
                        'page' => Yii::$app->request->get('page'),
                    ],
                ],
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->propertyIds)) {
            $formattedProperties = [];
            foreach ($this->propertyIds as $ids) {
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        $formattedProperties[] = $id;
                    }
                } elseif ($ids != '') {
                    $formattedProperties[] = $ids;
                }
            }
            $query->andFilterWhere(
                ['in', 'product_property.property_id', array_values($formattedProperties)]
            );
        }

        $query->andFilterWhere(
            [
                'category_id' => $this->category_id,
            ]
        );

        if (Yii::$app->request->get('sort')) {
            $query->orderBy($this->sort->orders);
        } else {
            $query->orderBy('product.updated_at DESC');
        }

        return $dataProvider;
    }
}
