<?php

namespace common\modules\shop\models;

use common\components\BaseModel;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
        $count = 0;
        if ($cart) {
            $count = ArrayHelper::getColumn($cart->products, 'qty');
            $count = array_sum($count);
        }
        return $count;
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
//            [ 'each', 'rule' => ['string']],
            [['updated_at', 'products', 'created_at'], 'safe'],
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

    public function removeCartItem($id)
    {
        $totalSum = 0;
        $key = $id;
        if (!empty($this->products)) {
            $cartItems = $this->products;
            foreach ($cartItems as $itemKey => $cartItem) {
                if ($itemKey == $key) {
                    unset($cartItems[$key]);
                } else {
                    $totalSum += $cartItem['price'] * $cartItem['qty'];
                }
            }
            $this->products = $cartItems;
            $this->update(false, ['products']);
        }
        return $totalSum;
    }
}
