<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\Item;
use \app\models\Event;
use \lithium\storage\Session;
use MongoId;

class CartController extends BaseController {

	public function view() {
		$this->_render['layout'] = 'cart';
		$cart = Cart::active(array('time' => '-3min'));
		foreach($cart as $item){
			$events = Event::find('all', array('conditions'=>array('_id' => $item->event[0])));
			$item->event= $events[0]->url;
		}
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
		$data = $this->request->data;

		if( $data ){
			$carts = $data['cart'];
			foreach( $carts as $key => $value){
				if(Cart::check($value['qty'], $value['_id'])){
					$cart = Cart::find('first',array('conditions' => array('_id' => $value['_id'])));
				}
			}
		}

		$this->render(array('layout' => false));
		echo json_encode($message);
	}

}

?>