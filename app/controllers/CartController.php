<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\Item;
use \lithium\storage\Session;

class CartController extends \lithium\action\Controller {
	
	public function index() {
		$this->_render['layout'] = 'cart';
		$carts = Cart::all();
		return compact('carts');
	}

	public function view() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::active();
		return compact('cart');
	}

	public function add() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::create();

		if ($this->request->query) {
			$itemId = $this->request->query['item_id'];
			$size = $this->request->query['item_size'];
			$item = Item::find('first', array(
				'conditions' => array(
					'_id' => "$itemId"),
				'fields' => array(
					'sale_retail', 
					"details.$size",
					'color',
					'description',
					'primary_images',
					'url'
			)));
			//Check if this item has already been added to cart
			$cartItem = Cart::find('first', array(
				'conditions' => array(
					'session' => Session::key(),
					'item_id' => "$itemId",
					'size' => "$size"
			)));
			if (!empty($cartItem)) {
				++ $cartItem->quantity;
				$cartItem->save();
				$this->redirect(array('Cart::view'));
			} else {
				$item = $item->data();
				$item['size'] = $size;
				$item['item_id'] = $itemId;
				unset($item['details']);
				unset($item['_id']);
				$info = array_merge($item, array('quantity' => 1));
				if ($cart->addFields() && $cart->save($info)) {
					$this->redirect(array('Cart::view'));
				}
			}
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
		$this->render(array('layout' => false));
		return compact('cart');
	}
	
	public function remove() {

		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				Cart::remove(array('_id' => "$key"));
			}
		}
		$this->render(array('layout' => false));
		
		$cartcount = Cart::itemCount();
		return compact('cartcount');
	}

	public function update() {
		if ($this->request->query) {
			$data = $this->request->query;
			$cart = Cart::find('first', array(
				'conditions' => array(
					'_id' => $data['_id']
			)));
			$cart->quantity = $data['qty'];
			if ($cart->save()) {
				$sucess = true;
			}
		}
		$this->render(array('layout' => false));

		return compact('success');
	}
}

?>