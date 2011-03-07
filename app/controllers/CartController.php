<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\Item;
use \app\models\Event;
use \lithium\storage\Session;
use MongoId;

/**
 * The Cart Class
 */
class CartController extends BaseController {

	/**
	 * The view method shows the current state of the cart.
	 *
	 * @return compact
	 */
	public function view() {
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

	/**
	 * The add method increments the quantity of one item.
	 *
	 * @return compact
	 */
	public function add() {
		$cart = Cart::create();
		$message = null;
		if ($this->request->data) {
			$itemId = $this->request->data['item_id'];
			$size = (empty($this->request->data['item_size'])) ? "no size": $this->request->data['item_size'];
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
		if ($this->request->data) {
				$data = $this->request->data;
				$cart = Cart::find('first', array(
					'conditions' => array(
						'_id' => $data["id"]
				)));
				if(!empty($cart)){
					Cart::remove(array('_id' => $data["id"]));
				}
			}
		$this->_render['layout'] = false;
		$cartcount = Cart::itemCount();
		return compact('cartcount');
	}

	public function update() {
		$success = false;
		$message = null;
		$data = $this->request->data;

		if( $data ){
			$carts = $data['cart'];
			foreach($carts as $id => $qty){
				$result = Cart::check((int)$qty, (string)$id);
				$cart = Cart::find('first' , array( 'conditions' => 		array('_id' =>  (string)$id)
					));

				if($result['status']){
					$cart->quantity = (int)$qty;
					$cart->save();
				}else{
					$cart->error = $result['errors'];
					$cart->save();
				}
			}
		}
	//	$this->redirect('Cartcontroller::view');
	}

}

?>