<?php

namespace app\controllers;

use \app\controllers\BaseController;
use \app\models\Cart;
use \app\models\Item;
use \lithium\storage\Session;

class CartController extends BaseController {

	
	public function index() {
		$carts = Cart::all();
		return compact('carts');
	}

	public function view() {
		$cart = Cart::all(array(
			'conditions' => array(
				'session' => Session::key()
		)));
		return compact('cart');
	}

	public function add() {
		$cart = Cart::create();

		if ($this->request->data) {
			$itemId = $this->request->data['item_id'];
			$size = $this->request->data['item_size'];
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
		return compact('cart');
	}
}

?>