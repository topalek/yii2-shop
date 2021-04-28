<?php

namespace common\modules\shop\models;

use common\components\BaseModel;
use common\modules\params\models\Params;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "shop_order".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $email
 * @property string  $phone
 * @property string  $delivery_info
 * @property array   $products
 * @property integer $status
 * @property string  $updated_at
 * @property string  $created_at
 */
class Order extends BaseModel
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROCESS = 1;
    const STATUS_CLOSED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['delivery_info'], 'string'],
            [['updated_at', 'products', 'created_at'], 'safe'],
            ['status', 'integer'],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => 'Имя',
            'email'         => 'Email',
            'phone'         => 'Телефон',
            'delivery_info' => 'Информация о доставке',
            'products'      => 'Товары',
            'status'        => 'Статус',
            'updated_at'    => 'Дата обновления',
            'created_at'    => 'Дата создания',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value'      => new Expression('NOW()'),
            ],
        ];
    }

    public function sendOrder()
    {
        $email = 'order@' . Yii::$app->request->getHostName();
        if ($this->email) {
            Yii::$app->mailer->compose('user_order', ['model' => $this])->setFrom(
                [$email ?? Yii::$app->params['senderEmail'] => 'Магазин']
            )
                ->setTo($this->email)
                ->setSubject('Ваш заказ')
                ->send();
        }

        return Yii::$app->mailer->compose('new_order', ['model' => $this])
            ->setFrom([$email ?? Yii::$app->params['senderEmail'] => 'Магазин'])
            ->setTo(Params::getShopEmail() ?? Yii::$app->params['supportEmail'])
            ->setSubject('Новый заказ')
            ->send();
    }

    public function getStatusLabel()
    {
        $name = self::statusList()[$this->status];
        switch ($this->status) {
            case self::STATUS_NEW:
                return Html::tag('span', $name, ['class' => 'label label-info']);
                break;
            case self::STATUS_IN_PROCESS:
                return Html::tag('span', $name, ['class' => 'label label-primary']);
                break;
            case self::STATUS_CLOSED:
                return Html::tag('span', $name, ['class' => 'label label-success']);
        }
    }

    public static function statusList()
    {
        return [
            self::STATUS_NEW        => 'Новый',
            self::STATUS_IN_PROCESS => 'Выполняется',
            self::STATUS_CLOSED     => 'Выполнен',
        ];
    }
}
