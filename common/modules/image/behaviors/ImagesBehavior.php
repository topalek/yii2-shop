<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 11.05.15
 * Time: 15:58
 */

namespace common\modules\image\behaviors;

use common\modules\image\models\Image;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class ImagesBehavior extends Behavior
{

    public $model;
    public $attribute;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveImages',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveImages',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteImages',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (empty($this->attribute)) {
            throw new InvalidParamException("Invalid or empty \"{$this->attribute}\" array");
        }

        if ($this->model == null) {
            $this->model = mb_strtolower($this->owner->getModelName());
        } else {
            $this->model = mb_strtolower($this->model);
        }
    }

    /**
     * @param $event
     */
    public function saveImages($event)
    {
        $attributeName = $this->attribute;

        $this->owner->$attributeName = UploadedFile::getInstances($this->owner, $attributeName);

        if (($imageArray = $this->owner->$attributeName) != null) {
            $basePath = Yii::$app->basePath . '/../uploads/images/' . $this->model . '/' . $event->sender->id . '/';
            FileHelper::createDirectory($basePath);
            foreach ($imageArray as $file) {
                $name = substr(md5(microtime()), 0, 15) . '.' . $file->extension;
                $imageModel = new Image();
                $imageModel->model_id = $event->sender->id;
                $imageModel->model_name = $this->model;
                $imageModel->image = $name;
                $imageModel->ordering = $imageModel->getMaxOrder($imageModel->tableName());
                $imageModel->save(false);
                $file->saveAs($basePath . $name);
            }
        }
    }

    public function deleteImages($event)
    {
        $id = $this->owner->id;
        Yii::$app->db->createCommand(
            "DELETE FROM image WHERE model_id=$id AND model_name='" . $this->model . "'"
        )->execute();
        FileHelper::removeDirectory(
            Yii::$app->basePath . '/../uploads/images/' . $this->model . '/' . $event->sender->id . '/'
        );
    }
}
