<?php
/**
 * @link      https://github.com/himiklab/yii2-search-component-v2
 * @copyright Copyright (c) 2014 HimikLab
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace common\modules\search\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * For example:
 *
 * ```php
 * public function behaviors()
 * {
 *  return [
 *      'search' => [
 *         'class' => SearchBehavior::class,
 *         'searchScope' => function ($model) {
 *             $model->select(['title', 'body', 'url']);
 *             $model->andWhere(['indexed' => true]);
 *         },
 *         'searchFields' => function ($model) {
 *             return [
 *                 ['name' => 'title', 'value' => $model->title],
 *                 ['name' => 'body', 'value' => strip_tags($model->body)],
 *                 ['name' => 'url', 'value' => $model->url, 'type' => SearchBehavior::FIELD_KEYWORD],
 *                 ['name' => 'model', 'value' => 'page', 'type' => SearchBehavior::FIELD_UNSTORED],
 *             ];
 *         }
 *      ],
 *  ];
 * }
 * ```
 *
 * @author  HimikLab
 * @package himiklab\yii2\search\behaviors
 */
class SearchBehavior extends Behavior
{
    const FIELD_TEXT = 'text';

    /* Fields are stored, indexed, and tokenized. Text fields are appropriate for storing
    information like subjects and titles that need to be searchable as well as returned with search results. */
    const FIELD_KEYWORD = 'keyword';

    /* Fields are stored and indexed, meaning that they can be searched as well as displayed
    in search results. They are not split up into separate words by tokenization. */
    const FIELD_BINARY = 'binary';

    /* Fields are not tokenized or indexed, but are stored for retrieval with search hits.
    They can be used to store any data encoded as a binary string, such as an image icon. */
    const FIELD_UNINDEXED = 'unIndexed';

    /* Fields are not searchable, but they are returned with search hits. Database timestamps, primary keys,
    file system paths, and other external identifiers are good candidates for UnIndexed fields. */
    const FIELD_UNSTORED = 'unStored';

    /* Fields are tokenized and indexed, but not stored in the index. Large amounts of text are best indexed using
    this type of field. Storing data creates a larger index on disk, so if you need to search but not redisplay
    the data, use an UnStored field. */
    /** @var callable */
    public $searchFields;
    /** @var callable */
    public $searchScope;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'addToIndex',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'updateIndex',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteIndex',
        ];
    }

    public function init()
    {
        //        if (!is_callable($this->searchFields)) {
        //            throw new InvalidConfigException('SearchBehavior::$searchFields isn\'t callable.');
        //        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchModels()
    {
        /** @var \yii\db\ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find();
        //        if (is_callable($this->searchScope)) {
        //            call_user_func($this->searchScope, $query);
        //        }
        if (method_exists($owner, 'getSearchScope')) {
            $scope = $owner->getSearchScope();
            $query->select($scope['select']);
            if (isset($scope['joinWith'])) {
                $query->joinWith($scope['joinWith']);
            }
            if (isset($scope['wish'])) {
                $query->with($scope['with']);
            }
            if (isset($scope['where'])) {
                $query->where($scope['where']);
            }
        }

        return $query;
    }

    public function addToIndex()
    {
        $owner = $this->owner;
        $search = \Yii::$app->search;
        $search->add($owner->getindexFields());
    }

    public function updateIndex()
    {
        $search = \Yii::$app->search;
        $searchFields = $this->owner->getindexFields();
        $search->updateIndex($searchFields);
    }

    public function deleteIndex()
    {
        $search = \Yii::$app->search;
        $owner = $this->owner;
        $searchFields = $owner->getindexFields();
        $search->deleteFromIndex($searchFields);
    }
}
