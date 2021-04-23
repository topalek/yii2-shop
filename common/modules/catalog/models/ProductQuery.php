<?php

namespace common\modules\catalog\models;

/**
 * This is the ActiveQuery class for [[Product]].
 *
 * @see Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{

    public function active()
    {
        $this->andWhere(['status' => 1]);
        return $this;
    }

    public function category($category_id)
    {
        $this->andWhere(['category_id' => $category_id]);
        return $this;
    }

    public function popular()
    {
        $this->orderBy('order_count DESC');
        return $this;
    }

    /**
     * @inheritdoc
     * @return Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
