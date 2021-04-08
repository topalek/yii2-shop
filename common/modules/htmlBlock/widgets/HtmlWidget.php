<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 18.06.14
 * Time: 10:12
 */

namespace common\modules\htmlBlock\widgets;

use common\modules\htmlBlock\models\HtmlBlock;
use yii\base\Widget;

class HtmlWidget extends Widget
{
    const TYPE_DEFAULT = 1;
    const TYPE_WITH_TITLE = 2;
    public $position;
    public $template = '_html';
    public $getAll = false;
    public $type = self::TYPE_DEFAULT;
    public $showEmptyMessage = true;

    public function init()
    {
        if ($this->type == self::TYPE_WITH_TITLE) {
            $this->template = '_html_with_title';
        }
    }

    public function run()
    {
        if ($this->getAll) {
            $models = HtmlBlock::getDb()->cache(
                function () {
                    return HtmlBlock::find()->where(
                        ['position' => $this->position, 'status' => HtmlBlock::STATUS_PUBLISHED]
                    )->orderBy('ordering ASC')->all();
                },
                3600,
                HtmlBlock::getDbDependency()
            );
            if ($models != null) {
                return $this->render(
                    $this->template,
                    [
                        'models' => $models,
                    ]
                );
            } elseif ($this->showEmptyMessage) {
                return '<h5>Створіть HtmlBlock з пизицією <strong>' . $this->position . '</strong></h5>';
            }
        } else {
            $model = HtmlBlock::getDb()->cache(
                function () {
                    return HtmlBlock::find()->where(
                        ['position' => $this->position, 'status' => HtmlBlock::STATUS_PUBLISHED]
                    )->one();
                },
                3600,
                HtmlBlock::getDbDependency()
            );

            if ($model != null) {
                return $this->render(
                    $this->template,
                    [
                        'model' => $model,
                    ]
                );
            } elseif ($this->showEmptyMessage) {
                return '<h5>Створіть HtmlBlock з пизицією <strong>' . $this->position . '</strong></h5>';
            }
        }

        return false;
    }
}
