<?php

namespace backend\extensions\fileapi\behaviors;

use ReflectionClass;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Security;

/**
 * Class UploadBehavior
 *
 * @package backend\extensions\fileapi\behaviors
 * Поведение для загрузки файлов.
 *
 * Пример использования:
 * ```
 * ...
 * 'uploadBehavior' => [
 *     'class' => UploadBehavior::class,
 *     'attributes' => ['avatar'],
 *     'deleteScenarios' => [
 *         'avatar' => 'delete-avatar',
 *     ],
 *     'scenarios' => ['signup', 'update'],
 *     'path' => Yii::getAlias('@my/path'),
 *     'tempPath' => Yii::getAlias('@my/tempPath'),
 * ]
 * ...
 * ```
 */
class UploadBehavior extends Behavior
{
    /**
     * @event Событие которое вызывается после успешной загрузки файла
     */
    const EVENT_AFTER_UPLOAD = 'afterUpload';

    /**
     * @var array Массив аттрибутов.
     */
    public $attributes = [];

    /**
     * @var array Массив сценариев в которых поведение должно срабатывать.
     */
    public $scenarios = [];

    /**
     * @var array Массив сценариев в которых нужно удалить указанные атрибуты и их файлы.
     */
    public $deleteScenarios = [];

    /**
     * @var string|array Путь к папке в которой будут загружены файлы.
     */
    public $path;

    /**
     * @var string|array Путь к временой папке в которой загружены файлы.
     */
    public $tempPath;

    /**
     * @var boolean В случае true текущий файл из атрибута модели будет удалён.
     */
    public $deleteOnSave = true;

    /**
     * @var array Массив событий поведения
     */
    protected $_events = [
        ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ActiveRecord::EVENT_AFTER_INSERT  => 'afterInsert',
    ];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!is_array($this->attributes) || empty($this->attributes)) {
            throw new InvalidParamException("Invalid or empty \"{$this->attributes}\" array");
        }
        if (empty($this->path)) {
            throw new InvalidParamException("Empty \"{$this->path}\".");
        } else {
            if (is_array($this->path)) {
                foreach ($this->path as $attribute => $path) {
                    $this->path[$attribute] = FileHelper::normalizePath($path) . DIRECTORY_SEPARATOR;
                }
            } else {
                $this->path = FileHelper::normalizePath($this->path) . DIRECTORY_SEPARATOR;
            }
        }
        if (empty($this->tempPath)) {
            throw new InvalidParamException("Empty \"{$this->tempPath}\".");
        } else {
            if (is_array($this->tempPath)) {
                foreach ($this->tempPath as $attribute => $path) {
                    $this->tempPath[$attribute] = FileHelper::normalizePath($path) . DIRECTORY_SEPARATOR;
                }
            } else {
                $this->tempPath = FileHelper::normalizePath($this->tempPath) . DIRECTORY_SEPARATOR;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return $this->_events;
    }

    /**
     * Функция срабатывает в момент создания новой записи моедли.
     */
    public function beforeInsert()
    {
        if (in_array($this->owner->scenario, $this->scenarios) || empty($this->scenarios)) {
            foreach ($this->attributes as $attribute) {
                if ($this->owner->$attribute) {
                    if (is_file($this->getTempPath($attribute))) {
                        rename($this->getTempPath($attribute), $this->getPath($attribute));
                        $this->triggerEventAfterUpload();
                    } else {
                        unset($this->owner->$attribute);
                    }
                }
            }
        }
    }

    /**
     * @param $attribute
     *
     * @return string Временный путь загрузки файла.
     * @internal param string $fileName Атрибут для которого нужно вернуть путь загрузки.
     */
    public function getTempPath($attribute)
    {
        $fileName = $this->owner->$attribute;
        if (is_array($this->tempPath) && isset($this->tempPath[$attribute])) {
            $path = $this->tempPath[$attribute];
        } else {
            $path = $this->tempPath;
        }
        return $path . $fileName;
    }

    /**
     * @param string $attribute Атрибут для которого нужно вернуть путь загрузки.
     * @param bool   $old
     *
     * @return string Путь загрузки файла.
     * @throws \yii\base\Exception
     */
    public function getPath($attribute, $old = false)
    {
        if ($old === true) {
            $fileName = $this->owner->id . DIRECTORY_SEPARATOR . $this->owner->getOldAttribute($attribute);
        } else {
            $fileName = $this->owner->$attribute;
        }
        if (is_array($this->path) && isset($this->path[$attribute])) {
            $path = $this->path[$attribute];
        } else {
            $path = $this->path;
        }

        if (FileHelper::createDirectory($path)) {
            return $path . $fileName;
        }
        return null;
    }

    /**
     * Определяем событие [[EVENT_AFTER_UPLOAD]] для текущей модели.
     */
    protected function triggerEventAfterUpload()
    {
        // $event = new ModelEvent;
        // $this->owner->trigger(self::EVENT_AFTER_UPLOAD, $event);
        $this->owner->trigger(self::EVENT_AFTER_UPLOAD);
    }

    /**
     * Функция срабатывает в момент обновления существующей записи моедли.
     */
    public function beforeUpdate()
    {
        if (in_array($this->owner->scenario, $this->scenarios) || empty($this->scenarios)) {
            foreach ($this->attributes as $attribute) {
                if ($this->owner->isAttributeChanged($attribute)) {
                    if (is_file($this->getTempPath($attribute))) {
                        rename($this->getTempPath($attribute), $this->getOwnerModelPath($attribute));
                        if ($this->deleteOnSave === true && $this->owner->getOldAttribute($attribute)) {
                            $this->delete($attribute, true);
                        }
                        // Вызываем событие [[EVENT_AFTER_UPLOAD]]
                        $this->triggerEventAfterUpload();
                    } else {
                        $this->owner->setAttribute($attribute, $this->owner->getOldAttribute($attribute));
                    }
                }
            }
        }
        // Удаляем указаные атрибуты и их файлы если это нужно
        if (!empty($this->deleteScenarios) && in_array($this->owner->scenario, $this->deleteScenarios)) {
            foreach ($this->deleteScenarios as $attribute => $scenario) {
                if ($this->owner->scenario === $scenario) {
                    $file = $this->getOwnerModelPath($attribute);
                    if (is_file($file) && unlink($file)) {
                        $this->owner->$attribute = null;
                    }
                }
            }
        }
    }

    public function getOwnerModelPath($attribute)
    {
        $path = $this->path . DIRECTORY_SEPARATOR . $this->owner->id . DIRECTORY_SEPARATOR;
        FileHelper::createDirectory($path);
        return $path . $this->owner->$attribute;
    }

    /**
     * Удаляем старый файл.
     *
     * @param      $attribute
     * @param bool $old
     *
     * @internal param string $fileName Имя файла.
     */
    protected function delete($attribute, $old = false)
    {
        $file = $this->getPath($attribute, $old);
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Функция срабатывает в момент удаления существующей записи моедли.
     */
    public function beforeDelete()
    {
        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                $this->delete($attribute);
            }
        }
    }

    public function afterInsert()
    {
        if (in_array($this->owner->scenario, $this->scenarios) || empty($this->scenarios)) {
            foreach ($this->attributes as $attribute) {
                if ($this->owner->$attribute) {
                    if (is_file($this->getPath($attribute))) {
                        rename($this->getPath($attribute), $this->getOwnerModelPath($attribute));
                        $this->triggerEventAfterUpload();
                    } else {
                        unset($this->owner->$attribute);
                    }
                }
            }
        }
    }

    public function getOwnerModelName()
    {
        $reflect = new ReflectionClass($this->owner->className());
        return strtolower($reflect->getShortName());
    }
}