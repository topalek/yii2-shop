<?php

namespace common\modules\catalog\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\helpers\ArrayHelper;

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
                    'article',
                    'additional_images',
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $categoryId = ArrayHelper::remove($params, 'id');
        $filter = ArrayHelper::getValue($params, 'filter');
        $lang = Yii::$app->language;
//        dd([
//            Yii::$app->request->pathInfo,
//            $params
//           ]);
        $this->sort = new Sort(
            [
                'attributes' => [
                    /*                    'title' => [
                                            'asc'     => ['title_' . $lang => SORT_ASC],
                                            'desc'    => ['title_' . $lang => SORT_DESC],
                                            'default' => SORT_ASC,
                                            'label'   => Yii::t('catalog', 'Название'),
                                        ],*/
                    'price' => [
                        'asc'     => ['price' => SORT_ASC],
                        'desc'    => ['price' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label'   => Yii::t('catalog', 'Цена'),
                    ],
                ],
                'route'      => Yii::$app->request->pathInfo,
                'params'     => $params,
            ]
        );

        $query = Product::find();

        if (!Yii::$app->request->get('sort')) {
            $query->orderBy('id DESC');
        }
        if ($categoryId) {
            $query->filterWhere(
                [
                    'category_id' => $categoryId,
                ]
            );
        }

        if ($filter) {
            $filter = explode(',', $filter);
            $query->leftJoin('{{%product_property}}', '{{%product_property}}.product_id={{%product}}.id');
            foreach ($filter as $f) {
                $query->andFilterWhere(
                    [
                        '{{%product_property}}.property_id' => $f,
                    ]
                );
            }
        }
//dd($query->createCommand()->rawSql);
        $query->joinWith('seo');
        return new ActiveDataProvider(
            [
                'query'      => $query,
                'sort'       => [
                    'attributes' => [
                        'price' => [
                            'asc'     => ['price' => SORT_ASC],
                            'desc'    => ['price' => SORT_DESC],
                            'default' => SORT_DESC,
                            'label'   => Yii::t('catalog', 'Цена'),
                        ],
                    ],
                    'route'      => Yii::$app->request->pathInfo,
                    'params'     => $params,
                ],
                'pagination' => [
                    'route'    => Yii::$app->request->pathInfo,
                    'params'   => $params,
                    'pageSize' => 9,
                ],
            ]
        );
    }


    public function adminSearch($params)
    {
        $viewType = Yii::$app->session->get('viewType', 'block');


        $query = Product::find();
        $query->with(['seo', 'category']);
        $query->leftJoin('product_property', 'product_property.product_id=product.id');
        $query->groupBy('product.id');
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
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
