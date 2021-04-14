<?php

namespace common\modules\shop\models;

use common\components\BaseModel;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "shop_cart".
 *
 * @property integer $id
 * @property string  $sid
 * @property array   $products
 * @property string  $updated_at
 * @property string  $created_at
 *
 */
class Cart extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    public static function getItemsCount()
    {
        $cart = self::getSessionCart();
        if ($cart) {
            return count($cart->products);
        } else {
            return 0;
        }
    }

    /**
     * @return array|null|ActiveRecord|static
     */
    static function getSessionCart()
    {
        $cartId = Yii::$app->request->cookies->getValue('cartId');
        $cart = null;
        if ($cartId) {
            $cart = self::findOne($cartId);
        }
        if (!$cart) {
            $cart = self::find()->where(['sid' => Yii::$app->session->id])->one();
        }
        return $cart;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sid'], 'required'],
            ['products', 'each', 'rule' => ['string']],
            [['updated_at', 'created_at'], 'safe'],
            [['sid'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'sid'        => 'Sid',
            'products'   => 'Товары',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата создания',
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

    public function beforeSave($insert)
    {
        $this->products = Json::encode($this->cartItems);
        return parent::beforeSave($insert);
    }

    public function removeCartItem($id, $charId = null)
    {
        $totalSum = 0;
        if ($charId == 0) {
            $charId = null;
        }
        $key = $id;
        if ($charId) {
            $key .= '_' . $charId;
        }
        if (!empty($this->products)) {
            foreach ($this->products as $itemKey => $cartItem) {
                if ($itemKey == $key) {
                    unset($this->products[$key]);
                    $this->update(false, ['products']);
                } else {
                    $totalSum += $cartItem['price'] * $cartItem['qty'];
                }
            }
        }
        return $totalSum;
    }
}
