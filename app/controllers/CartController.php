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
	*
	* @see app/models/Cart::increaseExpires()
	* @see app/models/Cart::active()
	* @return compact
	*/
	public function view() {
		if ($this->request->data) {
			$this->update();
		}
		Cart::increaseExpires();
		$message = '';
		$itemlist = array();
		$cart = Cart::active(array('time' => '-3min'));
		foreach($cart as $item){
			if (array_key_exists('error', $item->data()) && !empty($item->error)){
				$message .= $item->error . '<br/>';
				$item->error = "";
				$item->save();
			}
			$events = Event::find('all', array('conditions' => array('_id' => $item->event[0])));
			$itemInfo = Item::find('first', array('conditions' => array('_id' => $item->item_id)));
			$item->event_url = $events[0]->url;
			$item->available = $itemInfo->details->{$item->size} - Cart::reserved($item->item_id, $item->size);
			$itemlist[$item->created->sec] = $item->event[0];
		}
		$shipDate = Cart::shipDate($cart);
		if ($cart) {
			krsort($itemlist);
			$conditions = array('_id' => current($itemlist));
			$event = Event::find('first', compact('conditions'));
			if ($event) {
				$returnUrl = $event->url;
			}
		}
		//calculate savings
		$savings = Session::read('userSavings');
		return compact('cart', 'message', 'shipDate', 'returnUrl', 'savings');
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
					'discount_exempt'
			)));
			$cartItem = Cart::checkCartItem($itemId, $size);
			$itemInfo = Item::find('first', array('conditions' => array('_id' => $itemId)));
			if (!empty($cartItem)) {
				$avail = $itemInfo->details->{$itemInfo->size} - Cart::reserved($itemId, $itemInfo->size);
				//Make sure user does not add more than 9 items to the cart
				if($cartItem->quantity < 9 ){
					//Make sure the items are available
					if( $avail > 0 ){
						++$cartItem->quantity;
						$cartItem->save();
						//calculate savings
						$item[$item['_id']] = $cartItem->quantity;
						$this->savings($item, 'add');
					}else{
						$cartItem->error = 'You can’t add this quantity in your cart. <a href="#5">Why?</a>';
					$cartItem->save();
					}
				}else{
					$cartItem->error = 'You have reached the maximum of 9 per item.';
					$cartItem->save();
				}
			} else {
				$item = $item->data();
				$item['size'] = $size;
				$item['item_id'] = $itemId;
				unset($item['details']);
				unset($item['_id']);
				$info = array_merge($item, array('quantity' => 1));
				if ($cart->addFields() && $cart->save($info)) {
					//calculate savings
					$item[$itemId] = 1;
					$this->savings($item, 'add');
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
				$quantity = $cart->quantity;
				if(!empty($cart)){
					Cart::remove(array('_id' => $data["id"]));
					//calculate savings
					$item[$cart->item_id] = $quantity;
					$this->savings($item, 'remove');
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
		if ($data) {
			$carts = $data['cart'];
			foreach ($carts as $id => $quantity) {
				$result = Cart::check((integer) $quantity, (string) $id);
				$cart = Cart::find('first', array(
					'conditions' => array('_id' =>  (string) $id)
				));
				if ($result['status']) {
					if($quantity == 0){
				        Cart::remove(array('_id' => $id));
				    }else {
						$cart->quantity = (integer) $quantity;
						$cart->save();
					}
				} else {
					$cart->error = $result['errors'];
					$cart->save();
				}
				//build temp array
				$items[$cart->item_id] = $quantity;
			}
			//calculate savings
			$this->savings($items, 'update');
		}
		
		$this->_render['layout'] = false;
		$this->redirect('/cart/view');
	}
	
	public function savings($items = null, $action) {
		if($action == "update"){
			$savings = 0;
			if(!empty($items)) {
				foreach($items as $key => $quantity) {
					$itemInfo = Item::find('first', array('conditions' => array('_id' => $key)));
					if(!empty($itemInfo->msrp)){
						$savings += $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
					}
				}
			}
		} else if($action == "add") {
			$savings = Session::read('userSavings');
			if(empty($savings)) {
				$savings = 0;
			}
			if(!empty($items)) {
				foreach($items as $key => $quantity) {
					$itemInfo = Item::find('first', array('conditions' => array('_id' => $key)));
					if(!empty($itemInfo->msrp)){
						$savings += $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
					}
				}
			}
		} else if($action == "remove") {
			$savings = Session::read('userSavings');
			foreach($items as $key => $quantity) {
				$itemInfo = Item::find('first', array('conditions' => array('_id' => $key)));
				if(!empty($itemInfo->msrp)){
					$savings -= $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
				}
			}
		}
		Session::write('userSavings', $savings);
	}
}

?>