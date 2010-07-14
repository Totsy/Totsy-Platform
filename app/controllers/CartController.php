<?php

namespace app\controllers;

use \app\models\Cart;

class CartController extends \lithium\action\Controller {

	public function index() {
		$carts = Cart::all();
		return compact('carts');
	}

	public function view() {
		$cart = Cart::first($this->request->id);
		return compact('cart');
	}

	public function add() {
		$cart = Cart::create();

		if (($this->request->data) && $cart->save($this->request->data)) {
			$this->redirect(array('Carts::view', 'args' => array($cart->id)));
		}
		return compact('cart');
	}

	public function edit() {
		if (!$cart = Cart::find($this->request->id)) {
			$this->redirect('Carts::index');
		}
		if (($this->request->data) && $cart->save($this->request->data)) {
			$this->redirect(array('Carts::view', 'args' => array($cart->id)));
		}
		return compact('cart');
	}
}

?>