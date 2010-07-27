<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\Item;
use \lithium\storage\Session;

class CartController extends BaseController {

	public function view() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::active();

		if ($this->request->data) {
			return array('data' => $this->request->data);
		}
		return compact('cart');
	}

	public function add() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::create();

		if ($this->request->query) {
			$itemId = $this->request->query['item_id'];
			$size = ($this->request->query['item_size'] == 'undefined') ? "no size": $this->request->query['item_size'];
			$item = Item::find('first', array(
				'conditions' => array(
					'_id' => "$itemId"),
				'fields' => array(
					'sale_retail', 
					"details.$size",
					'color',
					'description',
					'primary_images',
					'url',
					'category',
					'product_weight'
			)));
			//Check if this item has already been added to cart
			$cartItem = Cart::checkCartItem($itemId, $size);

			if (!empty($cartItem)) {
				++ $cartItem->quantity;
				$cartItem->save();
				Item::reserve($itemId, $size, 1);
			} else {
				$item = $item->data();
				$item['size'] = $size;
				$item['item_id'] = $itemId;
				unset($item['details']);
				unset($item['_id']);
				$info = array_merge($item, array('quantity' => 1));
				if ($cart->addFields() && $cart->save($info)) {
					Item::reserve($itemId, $size, 1);
				}
			}
			$this->redirect(array('Cart::view'));
		}

		return compact('cart');
	}
	
	public function remove() {

		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				$cart = Cart::find('first', array(
					'conditions' => array(
						'_id' => "$key"
				)));
				Cart::remove(array('_id' => "$key"));
				if (!empty($cart)) {
					Item::reserve($cart->item_id, $cart->size, -$cart->quantity);
				}
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
			$diff = $data['qty'] - $cart->quantity;
			if ($cart->save()) {
				$sucess = true;
				Item::reserve($cart->item_id, $cart->size, $diff);
			}
		}
		$this->render(array('layout' => false));

		return compact('success');
	}

}

?>