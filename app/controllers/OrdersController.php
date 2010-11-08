<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Order;
use app\models\Event;
use app\models\Promotion;
use app\models\Promocode;
use app\controllers\BaseController;
use lithium\storage\Session;
use lithium\util\Validator;
use MongoDate;
use li3_silverpop\extensions\Silverpop;

class OrdersController extends BaseController {
	
	public function index() {
		$user = Session::read('userLogin');
		$orders = Order::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']),
			'order' => array('date_created' => 'DESC')
		));

		return (compact('orders'));	
	}

	public function view($order_id) {
		$user = Session::read('userLogin');
		$order = Order::find('first', array(
			'conditions' => array(
				'order_id' => $order_id,
				'user_id' => (string) $user['_id']
		)));
		return compact('order');
	}

	public function add() {
		$data = $this->request->data;
		Session::delete('credit');
		Session::delete('promocode');
		$user = Session::read('userLogin');
		$billing = Address::menu($user);
		$shipping = Address::menu($user);
		$fields = array(
			'item_id',
			'color',
			'category',
			'description',
			'product_weight',
			'quantity',
			'sale_retail',
			'size',
			'url',
			'primary_image',
			'expires'
		);

		$order = Order::create();

		if (Cart::increaseExpires()){
			$showCart = Cart::active(array('fields' => $fields, 'time' => '-5min'));
			$cart = Cart::active(array('fields' => $fields, 'time' => '-3min'));
		}

		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());
		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal','order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr'
		);

		$cartEmpty = ($cart->data()) ? false : true;

		if ($this->request->data) {
			if (count($this->request->data) > 1) {
				$user = Session::read('userLogin');
				$user['checkout'] = $this->request->data;
				Session::write('userLogin', $user);
				$this->redirect('Orders::process');
			} else {
				$error = "Shipping and Delivery Information Missing";
			}
		}

		return $vars + compact('cartEmpty', 'showCart', 'error');
	}

	public function process() {
		$order = Order::create();
		$user = Session::read('userLogin');
		$data = $user['checkout'] + $this->request->data;
		$fields = array(
			'item_id',
			'color',
			'category',
			'description',
			'product_weight',
			'quantity',
			'sale_retail',
			'size',
			'url',
			'primary_image',
			'expires',
			'event',
			'discount_exempt'
		);
		$cart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$showCart = Cart::active(array('fields' => $fields, 'time' => '-5min'));
		$discountExempt = $this->_discountExempt($cart);
		foreach ($cart as $cartValue) {
			$event = Event::find('first', array(
				'conditions' => array('_id' => $cartValue->event[0])
			));
			$cartValue->event_name = $event->name;
			$cartValue->event_id = $cartValue->event[0];
			unset($cartValue->event);
		}
		$tax = 0;
		$shippingCost = 0;
		$billingAddr = $shippingAddr = null;

		if (isset($data['billing_shipping']) && $data['billing_shipping'] == '1') {
			$data['shipping'] = $data['billing'];
		}

		foreach (array('billing', 'shipping') as $key) {
			$var = $key . 'Addr';

			if (isset($data[$key])) {
				$addr = $data[$key];
				${$var} = Address::find('first', array(
					'conditions' => array(
						'_id' => $addr,
						'user_id' => (string) $user['_id']
				)));
			}
		}

		if ($shippingAddr) {
			$tax = array_sum($cart->tax($shippingAddr));
			$shippingCost = Cart::shipping($cart, $shippingAddr);
		}

		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());

		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));

		$orderCredit = Credit::create();

		if (Session::read('credit')) {
			$orderCredit->credit_amount = Session::read('credit');
		}

		if (isset($this->request->data['credit_amount'])) {
			$credit = number_format((float)$this->request->data['credit_amount'], 2);
			$lower = -0.999;
			$upper = (!empty($userDoc->total_credit)) ? $userDoc->total_credit + 0.01 : 0;
			$inRange = Validator::isInRange($credit, null, compact('lower', 'upper'));
			$isMoney = Validator::isMoney($credit);
			if (!$isMoney) {
				$orderCredit->error = "Please apply credits that are in the form of $0.00";
				$orderCredit->errors(
					$orderCredit->errors() + array('amount' => "Please apply credits that are in the form of $0.00")
				);
			}
			if (!$inRange) {
				$orderCredit->errors(
					$orderCredit->errors() + array(
						'amount' => "Please apply credits that are greater than $0 and less than $$userDoc->total_credit"
					));
			}
			$isValid = ($subTotal >= $credit) ? true : false;
			if (!$isValid) {
				$orderCredit->errors(
					$orderCredit->errors() + array(
						'amount' => "Please apply credits that is $$subTotal or less"
					));
			}
			if ($isMoney && $inRange && $isValid) {
				$orderCredit->credit_amount = -$credit;
				Session::write('credit', -$credit);
			}
		}

		$orderPromo = Promotion::create();
		$postCreditTotal = $subTotal + $orderCredit->credit_amount;
		if (Session::read('promocode')) {
			$orderPromo->code = Session::read('promocode');
		}
		if (isset($this->request->data['code'])) {
			$orderPromo->code = $this->request->data['code'];
		}

		if ($orderPromo->code) {
			$code = Promocode::confirmCode($orderPromo->code);
			if (empty($code)) {
				$orderPromo->errors(
					$orderPromo->errors() + array(
						'promo' => 'Sorry, Your promotion code is invalid'
				));
			} else {
				if ($postCreditTotal > $code->minimum_purchase) {
					$orderPromo->user_id = $user['_id'];
					if ($code->type == 'percentage') {
						$orderPromo->saved_amount = $postCreditTotal * -$code->discount_amount;
					}
					if ($code->type == 'dollar') {
						$orderPromo->saved_amount = -$code->discount_amount;
					}
					Session::write('promocode', $orderPromo->code);
				} else {
					$orderPromo->errors(
						$orderPromo->errors() + array(
							'promo' => "Sorry, you need a minimum order total of $$code->minimum_purchase to use promotion code. Shipping and sales tax is not included."
					));
				}
			}
		}

		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal', 'order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr', 'orderCredit', 'orderPromo', 'userDoc', 'discountExempt'
		);

		if (($cart->data()) && (count($this->request->data) > 1) && $order->process($user, $data, $cart, $orderCredit, $orderPromo)) {
			$orderId = strtoupper(substr((string)$order->_id, 0, 8));
			$orderNumCheck = Order::count(array('order_id' => $orderId));
			if ($orderNumCheck > 0) {
				$order->order_id = $orderId.strtoupper(substr((string)$order->_id, 13, 4));
			} else {
				$order->order_id = $orderId;
			}
			if ($orderCredit->credit_amount) {
				User::applyCredit($user['_id'], $orderCredit->credit_amount);
				Credit::add($orderCredit, $user['_id'], $orderCredit->credit_amount, "Used Credit");
				Session::delete('credit');
				$order->credit_used = $orderCredit->credit_amount;
			}
			if ($orderPromo->saved_amount) {
				Promocode::add((string) $code->_id, $orderPromo->saved_amount, $order->total);
				$orderPromo->order_id = (string) $order->_id;
				$orderPromo->date_created = new MongoDate();
				$orderPromo->save();
				$order->promo_code = $orderPromo->code;
				$order->promo_discount = $orderPromo->saved_amount;
			}
			$order->save();
			Cart::remove(array('session' => Session::key()));
			foreach ($cart as $item) {
				Item::sold($item->item_id, $item->size, $item->quantity);
			}
			$user = User::getUser();
			++$user->purchase_count;
			$user->save(null, array('validate' => false));
			$data = array(
				'order' => $order,
				'email' => $user->email
			);
			Silverpop::send('orderConfirmation', $data);
			return $this->redirect(array('Orders::view', 'args' => $order->order_id));
		}

		$cartEmpty = ($cart->data()) ? false : true;

		return $vars + compact('cartEmpty', 'order', 'showCart');

	}

	protected function _discountExempt($cart) {
		$discountExempt = false;
		foreach ($cart as $cartItem) {
			if ($cartItem->discount_exempt) {
				$discountExempt = true;
			}
		}
		return $discountExempt;
	}
}

?>