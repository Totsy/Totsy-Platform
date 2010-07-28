<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Order;
use lithium\storage\Session;

class OrdersController extends \app\controllers\BaseController {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'main';
	}
	
	public function index() {
		$user = Session::read('userLogin');
		$orders = Order::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']
		)));

		return (compact('orders'));	
	}
	
	public function view() {
		$transaction = Order::first($this->request->id);
		return compact('order');
	}

	public function add() {
		$data = $this->request->data;
		$order = Order::create();
		$user = Session::read('userLogin');
		$billing = Address::menu($user, 'Billing');
		$shipping = Address::menu($user, 'Shipping');
		$cart = Cart::active();

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
				${$var} = is_array($addr) ? Address::create($addr) : Address::first($addr);
			}
			if (count(${$key}) && !${$var}) {
				${$var} = Address::first(isset($data[$key]) ? $data[$key] : key(${$key}));
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

		if ($this->request->is('ajax')) {
			return $vars;
		}

		if (($data = $this->request->data) && $order->process($user, $data, $cart)) {
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
		return $vars + compact('order');
	}
}

?>