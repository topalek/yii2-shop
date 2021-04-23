<?php

namespace common\modules\catalog\models;

/**
 * This is the ActiveQuery class for [[PropertyCategory]].
 *
 * @see PropertyCategory
 */
class PropertyCategoryQuery extends \yii\db\ActiveQuery
{
    public function forFilters()
    {
        $this->andWhere(['in_filters' => 1]);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return PropertyCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PropertyCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
