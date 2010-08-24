<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Order;
use app\models\Event;
use app\controllers\BaseController;
use lithium\storage\Session;
use lithium\util\Validator;
use app\extensions\Mailer;

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
			'event'
		);
		$cart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$showCart = Cart::active(array('fields' => $fields, 'time' => '-5min'));

		foreach ($cart as $cartValue) {
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $cartValue->event[0]
			)));
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

		if (isset($this->request->data['credit_amount'])) {
			$credit = (float) $this->request->data['credit_amount'];
			$lower = -0.999999;
			$upper = (!empty($userDoc->total_credit)) ? $userDoc->total_credit : 0;
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
			}
		}

		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal', 'order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr', 'orderCredit'
		);

		if (($cart->data()) && (count($this->request->data) > 1) && $order->process($user, $data, $cart, $orderCredit)) {
			$orderId = strtoupper(substr((string)$order->_id, 0, 8));
			$order->order_id = $orderId;
			if ($orderCredit->credit_amount < 0) {
				$order->credit_used = $orderCredit->credit_amount;
				User::applyCredit($user['_id'], $orderCredit->credit_amount);
				Credit::add($orderCredit, $user['_id'], $orderCredit->credit_amount, "Used Credit");
			}
			$order->save();
			Cart::remove(array('session' => Session::key()));
			foreach ($cart as $item) {
				Item::sold($item->item_id, $item->size, $item->quantity);
			}
			$user = User::getUser();
			++$user->purchase_count;
			$user->save();
			if ($user->purchase_count = 1) {
				if ($user->invited_by) {
					$credit = Credit::create();
					User::applyCredit($user->invited_by, Credit::INVITE_CREDIT);
					Credit::add($credit, $user->invited_by, Credit::INVITE_CREDIT, "Invitation");
				}
			}
			Mailer::send(
				'order',
				"Totsy - Order Acknowledgment - $orderId",
				array('name' => $user->firstname, 'email' => $user->email),
				compact('order')
			);
			return $this->redirect(array('Orders::view', 'args' => $order->order_id));
		}

		$cartEmpty = ($cart->data()) ? false : true;

		return $vars + compact('cartEmpty', 'order', 'showCart');

	}
}

?>