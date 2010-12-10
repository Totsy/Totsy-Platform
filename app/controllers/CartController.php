<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\Item;
use \lithium\storage\Session;

class CartController extends BaseController {

	public function view() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::active(array('time' => '-3min'));

		if ($this->request->data) {
			return array('data' => $this->request->data);
		}
		return compact('cart');
	}

	public function add() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::create();
		$message = null;
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
					'primary_image',
					'url',
					'category',
					'product_weight',
					'event',
					'vendor_style',
					'discount_exempt',
					'event'
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

				}
			}

			$this->redirect(array('Cart::view'));
		}

		return compact('cart', 'message');
	}
	
	public function remove() {

		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				$cart = Cart::find('first', array(
					'conditions' => array(
						'_id' => "$key"
				)));
				Cart::remove(array('_id' => "$key"));
			}
		}
		$this->render(array('layout' => false));
		
		$cartcount = Cart::itemCount();
		return compact('cartcount');
	}

	public function update() {
		$success = false;
		$message = null;
		if ($this->request->query) {
			$qty = (int) $this->request->query['qty'];
			if ($qty > 0) {
				$cart = Cart::find('first', array(
					'conditions' => array(
						'_id' => $this->request->query['_id']
				)));
				$diff = $qty - $cart->quantity;
				$cart->quantity = $qty;

				$item = Item::find('first', array(
					'conditions' => array(
						'_id' => $cart->item_id
				)));

				if ($item->details->{$cart->size} == 0) {
					$message = "Sorry we are sold out of this item.";
				}
				if ($cart->quantity > $item->details->{$cart->size}) {
					$message = "Sorry you have requested more of this item than what is available.";
				}
				if (empty($message) && $cart->save()) {
					$message = "Your cart has been updated.";
				}
			} else {
				$message = "Please submit a number greater than 0";
			}

		}
		$this->render(array('layout' => false));
		echo json_encode($message);
	}

}

?>