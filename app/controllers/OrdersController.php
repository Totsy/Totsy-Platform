<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Order;
use app\controllers\BaseController;
use lithium\storage\Session;

class OrdersController extends BaseController {
	
	public function index() {
		$user = Session::read('userLogin');
		$orders = Order::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']
		)));

		return (compact('orders'));	
	}

	public function view() {
		$user = Session::read('userLogin');

		$order = Order::find('first', array(
			'conditions' => array(
				'_id' => $this->request->id,
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
		$showCart = Cart::active(array('fields' => $fields, 'time' => '-5min'));
		$cart = Cart::active(array('fields' => $fields, 'time' => '-3min'));
		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());
		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal','order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr'
		);

		$cartEmpty = ($cart->data()) ? false : true;

		if ($this->request->data) {
			$user = Session::read('userLogin');
			$user['checkout'] = $this->request->data;
			Session::write('userLogin', $user);
			$this->redirect('Orders::process');
		}

		return $vars + compact('cartEmpty', 'showCart');
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
			'expires'
		);
		$cart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$showCart = Cart::active(array('fields' => $fields, 'time' => '-5min'));

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
		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal', 'order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr'
		);


		if (($cart->data()) && ($this->request->data) && $order->process($user, $data, $cart)) {
			Cart::remove(array('session' => Session::key()));
			foreach ($cart as $item) {
				Item::sold($item->item_id, $item->size, $item->quantity);
			}
			$user = User::getUser();
			$credit = Credit::create();
			++$user->purchase_count;
			$user->save();
			if ($user->purchase_count = 1) {
				if ($user->invited_by) {
					User::applyCredit($user->invited_by, Credit::INVITE_CREDIT);
					Credit::add($credit, $user->invited_by, Credit::INVITE_CREDIT, "Invitation");
				}
			}
			return $this->redirect(array('Orders::view', 'id' => (string) $order->_id));
		}

		$cartEmpty = ($cart->data()) ? false : true;

		return $vars + compact('cartEmpty', 'order', 'showCart');

	}
}

?>