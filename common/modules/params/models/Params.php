<?php

namespace common\modules\params\models;

use common\components\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "params".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $sys_name
 * @property string  $value
 * @property integer $status
 * @property integer $required
 */
class Params extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'params';
    }

    static function getInfoEmail()
    {
        return self::getParam('infoEmail');
    }

    private static function getParam($name)
    {
        return ArrayHelper::getValue(self::getParamsList(), $name);
    }

    public static function getParamsList()
    {
        $params = [];
        $data = Yii::$app->cache->get('system_params');
        if (!$data) {
            $data = self::find()->select(['sys_name', 'value'])->where(['status' => 1])->all();
            Yii::$app->cache->set('system_params', $data, 86400);
        }

        if ($data != null) {
            foreach ($data as $param) {
                $params[$param->sys_name] = $param->value;
            }
        }

        return $params;
    }

    static function getShopEmail()
    {
        return self::getParam('shopEmail');
    }

    static function getSitePhones()
    {
        return self::getParam('sitePhones');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sys_name', 'value'], 'required'],
            [['value'], 'string'],
            [['status'], 'integer'],
            [['name', 'sys_name'], 'string', 'max' => 255],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'name'     => 'Название',
            'sys_name' => 'Системное название',
            'value'    => 'Значення',
            'status'   => 'Активный',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::updateParamsCache();
        parent::afterSave($insert, $changedAttributes);
    }

    static function updateParamsCache()
    {
        $data = self::find()->select(['sys_name', 'value'])->where(['status' => 1])->all();
        Yii::$app->cache->set('system_params', $data, 86400);
    }

    public function afterDelete()
    {
        self::updateParamsCache();
        parent::afterDelete();
    }
}
