<?php

namespace app\controllers;

use app\models\Cart;
use app\models\Address;
use app\models\Transaction;
use lithium\storage\Session;

class TransactionsController extends \app\controllers\BaseController {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'main';
	}

	public function view() {
		$transaction = Transaction::first($this->request->id);
		return compact('transaction');
	}

	public function add() {
		$data = $this->request->data;
		$order = Transaction::create();
		$user = Session::read('userLogin');
		$billing = Address::menu($user, 'Billing');
		$shipping = Address::menu($user, 'Shipping');
		$cart = Cart::findAllBySession(Session::key());

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
			return $this->redirect(array('Transactions::view', 'id' => (string) $order->_id));
		}
		return $vars + compact('order');
	}
}

?>