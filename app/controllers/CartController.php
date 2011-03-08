<?php

namespace app\controllers;

use app\models\Cart;
use app\models\Item;
use app\models\Event;
use lithium\storage\Session;
use MongoId;

/**
 * Facilitates the app CRUD operations of a users cart (baskets).
 * The cart is the first step in the checkout process. Users are able to modify the
 * quantities of an item in their cart and remove it altogether.
 * Carts are not active indefinitely. There a crontab that will remove all cart items
 * that are more than 15 minutes old.
 *
 * @todo Show link to cartcleaner.php
 * @see app/models/Cart
 */
class CartController extends BaseController {

	/**
	* Displays the current state of the cart.
	*
	* @see app/models/Cart::increaseExpires()
	* @see app/models/Cart::active()
	* @return compact
	*/
	public function view() {
		Cart::increaseExpires();
		$message = '';
		$cart = Cart::active(array('time' => '-3min'));
		foreach($cart as $item){
			if(array_key_exists('error', $item->data()) && !empty($item->error)){
				$message .= $item->error . '<br/>';
				$item->error = "";
				$item->save();
			}
			$events = Event::find('all', array('conditions'=>array('_id' => $item->event[0])));
			$item->event_url = $events[0]->url;
		}
		if ($this->request->data) {
			return array('data' => $this->request->data);
		}
		$shipDate = Cart::shipDate($cart);
		return compact('cart', 'message', 'shipDate');
	}

	/**
	 * The add method increments the quantity of one item.
	 *
	 * @see app/models/Cart::checkCartItem()
	 * @return compact
	 */
	public function add() {
		$cart = Cart::create();
		$message = null;
		if ($this->request->data) {
			$itemId = $this->request->data['item_id'];
			$size = (empty($this->request->data['item_size'])) ?
			 "no size": $this->request->data['item_size'];
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

	/**
	* The remove method delete an item from the temporary cart.
	*
	* @see app/models/Cart::remove()
	* @return compact
	*/
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

	/**
	* The update method allow to update the actual cart
	*
	* @see app/models/Cart::check()
	*/
	public function update() {
		$success = false;
		$message = null;
		$data = $this->request->data;
		if ($data){
			$carts = $data['cart'];
			foreach($carts as $id => $quantity){
				$result = Cart::check((integer) $quantity, (string) $id);
				$cart = Cart::find('first', array(
					'conditions' => array('_id' =>  (string) $id)
				));
				if ($result['status']) {
					$cart->quantity = (integer) $quantity;
					$cart->save();
				} else {
					$cart->error = $result['errors'];
					$cart->save();
				}
			}
		}
		$this->_render['layout'] = false;
		$this->redirect('/cart/view');
	}
}

?>