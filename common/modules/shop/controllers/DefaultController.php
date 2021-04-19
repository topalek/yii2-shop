<?php

namespace common\modules\shop\controllers;

use common\components\BaseController;
use common\modules\catalog\models\Product;
use common\modules\shop\models\Cart;
use common\modules\shop\models\Order;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default controller for the `shop` module
 */
class DefaultController extends BaseController
{
    public function actionAddToCart()
    {
        /**
         * @var $product Product
         */
        $request = Yii::$app->request;
        $productId = $request->get('id');
        $charId = (int)$request->get('charId');
        if ($charId == 0) {
            $charId = null;
        }
        $cartItems = [];
        $cart = Cart::getSessionCart();
        if ($cart) {
            $cartItems = $cart->products;
        } else {
            $cart = new Cart();
            $cart->sid = Yii::$app->session->id;
        }

        $product = Product::findById($productId);

        if ($product) {
            $key = $productId;
            if ($charId) {
                $key .= '_' . $charId;
            }

            if (isset($cartItems[$key])) {
                $cartItems[$key]['qty']++;
            } else {
                $newItem = [
                    'id'       => $productId,
                    'title_uk' => $product->title_uk,
                    'title_en' => $product->title_en,
                    'title_ru' => $product->title_ru,
                    'photo'    => $product->getMainImgUrl(),
                    'price'    => asMoney($product->price),
                    'qty'      => 1,
                    'url'      => $product->getSeoUrl(),
                ];

                if ($charId != null) {
                    foreach ($product->properties as $property) {
                        if ($property->id == $charId) {
                            $newItem['char_id'] = $charId;
                            $newItem['charTitle_uk'] = $property->propertyCategory->title_uk . ' ' . $property->property->title_uk;
                            $newItem['charTitle_en'] = $property->propertyCategory->title_en . ' ' . $property->property->title_en;
                            $newItem['charTitle_ru'] = $property->propertyCategory->title_ru . ' ' . $property->property->title_ru;
                            $newItem['price'] = asMoney($property->price);
                            $newItem['photo'] = $property->getImg();
                        }
                    }
                }

                $cartItems[$key] = $newItem;
            }

            $cart->products = $cartItems;
            if ($cart->save()) {
                Yii::$app->response->cookies->add(
                    new Cookie(
                        [
                            'name'  => 'cartId',
                            'value' => $cart->id,
                        ]
                    )
                );

                return $this->renderAjax('_cart', ['cartItems' => $cartItems]);
            } else {
                return $this->asJson(['errors' => $cart->getErrors()]);
            }
        } else {
            throw new BadRequestHttpException(Yii::t('site', 'Не правильный запрос'));
        }
    }

    public function actionDeleteCartItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cart = Cart::getSessionCart();
        if ($cart) {
            $newTotalSum = $cart->removeCartItem($id, (int)Yii::$app->request->get('charId'));
            return ['status' => true, 'totalSum' => $newTotalSum];
        } else {
            return ['status' => false, 'message' => Yii::t('site', 'Ваш кошик пустий')];
        }
    }

    public function actionChangeQty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cart = Cart::getSessionCart();
        if ($cart) {
            $request = Yii::$app->request;
            $itemId = $request->get('itemId');
            $qty = $request->get('qty');
            $charId = (int)$request->get('charId');
            if ($charId == 0) {
                $charId = null;
            }
            $needleKey = $itemId;
            if ($charId) {
                $needleKey .= '_' . $charId;
            }
            $totalPrice = 0;
            $cartItems = $cart->products;
            foreach ($cartItems as $key => $cartItem) {
                if ($key == $needleKey) {
                    $cartItem['qty'] = $qty;
                    $cartItems[$key]['qty'] = $qty;
                    $cart->update(false, ['products']);
                }
                $totalPrice += $cartItem['price'] * $cartItem['qty'];
            }

            return ['status' => true, 'totalPrice' => $totalPrice];
        }
        return ['status' => false, 'message' => Yii::t('site', 'Ваша корзина пустой')];
    }

    public function actionViewCart()
    {
        $cartItems = [];
        $cart = Cart::getSessionCart();
        if ($cart) {
            $cartItems = $cart->products;
        }
        return $this->renderAjax('_cart', ['cartItems' => $cartItems]);
    }

    public function actionOrder()
    {
        $request = Yii::$app->request;
        $cart = Cart::getSessionCart();
        $model = new Order();
        $cartItems = $cart->products;
        $model->products = $cartItems;

        if ($request->isAjax && $request->get('updateContainer') == true) {
            return $this->renderPartial('create_order', ['model' => $model]);
        }

        if (empty($cartItems)) {
            return $this->redirect(['/']);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load($request->post()) && $model->save()) {
            if ($model->sendOrder()) {
                $cart->products = [];
                $cart->update(false, ['products']);
                Yii::$app->session->setFlash('orderSuccess', true);
                return $this->redirect(['success']);
            }
        }

        return $this->render('create_order', ['model' => $model]);
    }

    public function actionSuccess()
    {
        if (Yii::$app->session->getFlash('orderSuccess') == true) {
            return $this->render('success_order');
        } else {
            return $this->redirect(['/']);
        }
    }

    public function actionIndex()
    {
        $query = Product::find()->with(['category'])->active();
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'pagination' => [
                    'pageSize' => 12,
                ],
            ]
        );
        return $this->render('shop', compact('dataProvider',));
    }
}
