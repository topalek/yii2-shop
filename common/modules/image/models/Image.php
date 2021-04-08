<?php

namespace common\modules\image\models;

use common\components\BaseModel;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\imagine\Image as ImageHelper;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property string  $model_name
 * @property integer $model_id
 * @property string  $image
 * @property integer $is_main
 * @property integer $ordering
 * @property string  $updated_at
 * @property string  $created_at
 */
class Image extends BaseModel
{
    const MAIN_IMAGE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    static public function getModelImages($model_id, $model_name, $width = null, $height = null, $escapeMain = false)
    {
        $sql = "SELECT * FROM image WHERE model_id=$model_id AND model_name='$model_name'";
        if ($escapeMain) {
            $sql .= " AND is_main=0";
        }
        $sql .= " ORDER BY id ASC";
        $models = self::getDb()->cache(
            function () use ($sql) {
                return Image::findBySql($sql)->all();
            },
            self::DEFAULT_CACHE_DURATION,
            self::getDbDependency()
        );

        if ($models != null) {
            if ($width != null) {
                if ($height == null) {
                    $height = $width;
                }
                $result = [];
                foreach ($models as $model) {
                    $image['url'] = $model->getImage($width, $height);
                    $image['fullSizeUrl'] = $model->getOriginalUrl();
                    $image['id'] = $model->id;
                    $image['is_main'] = $model->is_main === self::MAIN_IMAGE;
                    $result[] = $image;
                }
                return $result;
            }
            return $models;
        }
        return false;
    }

    public static function getImagesPreview($model_id, $model_name, $width = null, $height = null)
    {
        $images = [];
        $sql = "SELECT * FROM image WHERE model_id=$model_id AND model_name='$model_name' ORDER BY id ASC";
        $models = self::getDb()->cache(
            function () use ($sql) {
                return Image::findBySql($sql)->all();
            },
            3600,
            self::getDbDependency()
        );

        foreach (array_reverse($models) as $model) {
            $images[] =
                Html::img($model->getImage($width, $height), ['class' => 'file-preview-image']) .
                '<span class="delete-photo" title="Удалить" data-id="' . $model->id . '">
                        <i class="glyphicon glyphicon-trash"></i>
                    </span>';
        }
        return $images;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_name', 'model_id'], 'required'],
            [['model_id', 'is_main', 'ordering'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['model_name', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'model_name' => Yii::t('image', 'Назва моделі'),
            'model_id'   => Yii::t('image', 'Id моделі'),
            'image'      => Yii::t('image', 'Назва зображення'),
            'is_main'    => Yii::t('image', 'Головне зображення'),
            'ordering'   => Yii::t('image', 'Сортування'),
            'updated_at' => Yii::t('image', 'Дата оновления'),
            'created_at' => Yii::t('image', 'Дата создания'),
        ];
    }

    public function getOriginalUrl()
    {
        return Yii::$app->request->baseUrl . '/uploads/images/' . $this->model_name . '/' . $this->model_id . '/' . $this->image;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if ($this->checkForMainImage()) {
                $this->is_main = self::MAIN_IMAGE;
            }
        }
        return parent::beforeSave($insert);
    }

    protected function checkForMainImage()
    {
        $model_name = $this->model_name;
        $model_id = $this->model_id;
        $sql = "SELECT model_id, model_name, is_main FROM image WHERE model_id=$model_id AND model_name='$model_name' AND is_main=1";
        $result = self::getDb()->cache(
            function ($db) use ($sql) {
                return $db->createCommand($sql)->queryScalar();
            },
            self::DEFAULT_CACHE_DURATION,
            self::getDbDependency()
        );
        if ($result == null) {
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->is_main == self::MAIN_IMAGE) {
                $notMainImage = self::findBySql(
                    "SELECT * FROM image WHERE model_id=$this->model_id AND model_name='$this->model_name' AND is_main=0"
                )->one();
                if ($notMainImage != null) {
                    $notMainImage->is_main = self::MAIN_IMAGE;
                    $notMainImage->update(false, ['is_main']);
                }
            }
            @unlink($this->getOriginalPath() . $this->image);

            self::deleteThumbs($this->getThumbPath(), $this->image);

            return true;
        } else {
            return false;
        }
    }

    public function getOriginalPath()
    {
        return Yii::$app->basePath . '/../uploads/images/' . $this->model_name . '/' . $this->model_id . '/' . $this->image;
    }

    public static function deleteThumbs($path, $original_file_name)
    {
        if (is_dir($path)) {
            $dir = opendir($path);
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, $original_file_name)) {
                    @unlink($path . $file);
                }
            }
            closedir($dir);
        }
    }

    public function getThumbPath()
    {
        return Yii::$app->basePath . '/../uploads/images/' . $this->model_name . '/' . $this->model_id . '/thumb/';
    }

    public function getImage($width = null, $height = null)
    {
        return self::getThumb($width, $height, $this->model_name, $this->model_id, $this->image);
    }

    /**
     * @param       $width
     * @param       $height
     * @param       $model_name
     * @param       $model_id
     * @param       $image_name
     * @param array $params
     *
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public static function getThumb($width, $height, $model_name, $model_id, $image_name, $params = [])
    {
        $params_default = [
            'base_path'          => null,  // custom path to save thumb
            'native_dir'         => false, // save thumb to $model_name dir
            'original_file_path' => null,  // path to original file
        ];

        $model_name = strtolower($model_name);

        $params = ArrayHelper::merge($params_default, $params);

        if ($params['base_path'] == null) {
            if ($params['native_dir']) {
                $params['base_path'] = '/uploads/' . $model_name . '/' . $model_id . '/';
            } else {
                $params['base_path'] = '/uploads/images/' . $model_name . '/' . $model_id . '/';
            }
        }

        $basePath = $params['base_path'];

        if (php_sapi_name() == "cli") // check if php run from console
        {
            $baseUrl = $basePath;
        } else {
            $baseUrl = Yii::$app->request->baseUrl . $basePath;
        }

        $thumbUrl = $baseUrl . 'thumb/';

        if ($width != null && $height != null) {
            $name = $width . '_' . $height . '_' . $image_name;

            if ($params['original_file_path'] == null) {
                $originalPath = Yii::$app->basePath . '/..' . $basePath . $image_name;
            } else {
                $originalPath = Yii::$app->basePath . '/..' . $params['original_file_path'];
            }

            if (!file_exists($originalPath)) {
                return false;
            }

            $folderPath = Yii::$app->basePath . '/..' . $basePath . '/thumb/';
            $thumbPath = $folderPath . $name;
            if (!file_exists($thumbPath)) {
                $originalImgInfo = getimagesize($originalPath);
                $originalWidth = $originalImgInfo[0];
                $originalHeight = $originalImgInfo[1];

                if ($originalWidth == $width && $originalHeight == $height) {
                    return $baseUrl . $image_name;
                }

                FileHelper::createDirectory($folderPath, 0777);
                ImageHelper::getImagine()->open($originalPath)->thumbnail(
                    new Box($width + $width / 2, $height + $height / 2)
                )->save($thumbPath);

                $imgInfo = getimagesize($thumbPath);
                $imgWidth = $imgInfo[0];
                $imgHeight = $imgInfo[1];

                if ($imgWidth < $width) {
                    $newWidth = $width + ($width - $imgWidth);
                    $newHeight = round($width / $originalWidth * $originalHeight);
                    ImageHelper::getImagine()->open($originalPath)->thumbnail(new Box($newWidth, $newHeight))->save(
                        $thumbPath,
                        ['quality' => 100]
                    );
                    $imgInfo = getimagesize($thumbPath);
                    $imgWidth = $imgInfo[0];
                    $imgHeight = $imgInfo[1];
                }

                if ($imgHeight < $height) {
                    $newWidth = round($height / $originalHeight * $originalWidth);
                    $newHeight = $height + ($height - $imgHeight);
                    ImageHelper::getImagine()->open($originalPath)->thumbnail(new Box($newWidth, $newHeight))->save(
                        $thumbPath,
                        ['quality' => 100]
                    );
                    $imgInfo = getimagesize($thumbPath);
                    $imgWidth = $imgInfo[0];
                    $imgHeight = $imgInfo[1];
                }

                if ($imgWidth > $width || $imgHeight > $height) {
                    $startX = 0;
                    $startY = 0;
                    if ($imgWidth > $width) {
                        $startX = ceil($imgWidth - $width) / 2;
                    }
                    if ($imgWidth > $height) {
                        $startY = ceil($imgHeight - $height) / 2;
                    }
                    ImageHelper::getImagine()->open($thumbPath)->crop(
                        new Point($startX, $startY),
                        new Box($width, $height)
                    )->save($thumbPath, ['quality' => 100]);
                }
            }
            return $thumbUrl . $name;
        } else {
            return $baseUrl . $image_name;
        }
    }
}
