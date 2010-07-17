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
		$addresses = Address::find('list', array(
			'conditions' => array('user_id' => Session::read('_id'))
		));
		$cart = Cart::all(array(
			'conditions' => array('session' => Session::key())
		));
		$order = Transaction::create();
		$user = Session::read('userLogin');

		if (($data = $this->request->data) && $order->process($user, $data, $cart, $addresses)) {
			return $this->redirect(array('Transactions::view', 'id' => (string) $order->_id));
		}
		return compact('order', 'user', 'addresses', 'cart');
	}
}

?>