<?php

namespace app\controllers;

use app\models\Cart;
use app\models\Item;
use app\models\Event;
use app\models\User;
use app\models\Credit;
use app\models\Service;
use app\models\Promotion;
use lithium\storage\Session;
use MongoId;
use MongoDate;
use app\extensions\Mailer;

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
		#Initialize Datas
		$cartExpirationDate = 0;
		$shipping = 7.95;
		$shipping_discount = 0;
		$vars = compact('cartPromo','cartCredit', 'services');
		$cartPromo = null; 
		$cartCredit = null;
		$services = null;
		$message = '';	
		#Get Users Informations
		$user = Session::read('userLogin');
		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));
		#Update the Cart
		if (!empty($this->request->data)) {
			$this->update();
		}
		#Get current Discount
		$vars = Cart::getDiscount();
		Cart::increaseExpires();
		$cart = Cart::active(array('time' => '-3min'));
		$test = $cart->data();
		if(empty($test)) {
			#Remove Temporary Session Datas**/
			Session::delete('userSavings');	
			Session::delete('promocode');
			Session::delete('credit');
			Session::delete('services');	
		}
		$cartItemEventEndDates = Array();
		$i = 0;
		$subTotal = 0;
		$itemlist = array();
		foreach($cart as $item){
			if($cartExpirationDate < $item['expires']->sec) {
				$cartExpirationDate = $item['expires']->sec;
			}
			if (array_key_exists('error', $item->data()) && !empty($item->error)){
				$message .= $item->error . '<br/>';
				$item->error = "";
				$item->save();
			}
			$events = Event::find('all', array('conditions' => array('_id' => $item->event[0])));
			$itemInfo = Item::find('first', array('conditions' => array('_id' => $item->item_id)));
			
			$cartItemEventEndDates[$i] = $events[0]->end_date->sec;
						
			$item->event_url = $events[0]->url;
			$item->available = $itemInfo->details->{$item->size} - Cart::reserved($item->item_id, $item->size);
			$itemlist[$item->created->sec] = $item->event[0];
			$subTotal += $item->quantity * $item->sale_retail;
			$i++;
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
		#Calculate savings
		$userSavings = Session::read('userSavings');
		$savings = $userSavings['items'] + $userSavings['discount'] + $userSavings['services'];
		$postDiscount = ($subTotal - $vars['services']['tenOffFitfy']);
		$services = $vars['services'];
		if(!empty($vars['cartCredit'])) {
			$credits = Session::read('credit');
		 	$vars['postDiscountTotal'] -= $credits;
		}
		if(!empty($vars['cartPromo']['saved_amount'])) {
		 	$promocode = Session::read('promocode');
		 	if($promocode['type'] === 'free_shipping') {
				$shipping_discount = $shipping;
			}
		}
		if(!empty($services['freeshipping']['enable'])) {
			$shipping_discount = $shipping;
		}
		$total = $vars['postDiscountTotal'];
		return $vars + compact('cart', 'message', 'subTotal', 'services', 'total', 'shipDate', 'returnUrl', 'promocode', 'savings','shipping_discount', 'credits', 'userDoc','cartItemEventEndDates', 'cartExpirationDate');
	}

	/**
	 * The add method increments the quantity of one item.
	 *
	 * @see app/models/Cart::checkCartItem()
	 * @return compact
	 */
	public function add() {
		#Check Cart and Refresh Timer
		$this->refreshTimer();
		$cart = Cart::create();
		if ($this->request->data) {
			$itemId = $this->request->data['item_id'];
			$size = (!array_key_exists('item_size', $this->request->data)) ?
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
						Cart::updateSavings($item,'add');
					} else {
						$cartItem->error = 'You can’t add this quantity in your cart. <a href="#5">Why?</a>';
						$cartItem->save();
						$this->addIncompletePurchase(Cart::active());
					}
				}else{
					$cartItem->error = 'You have reached the maximum of 9 per item.';
					$cartItem->save();
					$this->addIncompletePurchase(Cart::active());
				}
			} else {
				$item = $item->data();
				$item_id = (string) $item['_id'];
				$item['size'] = $size;
				$item['item_id'] = $itemId;
				unset($item['details']);
				unset($item['_id']);
				$info = array_merge($item, array('quantity' => 1));
				if ($cart->addFields() && $cart->save($info)) {
					//calculate savings
					$item[$itemId] = 1;
					Cart::updateSavings($item, 'add');
					$this->addIncompletePurchase(Cart::active());
				}
			}
			$this->redirect(array('Cart::view'));
		}
		return compact('cart');
	}


	/**
	* The remove method delete an item from the temporary cart.
	*
	* @see app/models/Cart::remove()
	* @return compact
	*/
	public function remove($id = null) {
		if ($this->request->data) {
			$data = $this->request->data;
			if(!empty($id)) {
				$data["id"] = $id;
			}
			$cart = Cart::find('first', array(
				'conditions' => array(
					'_id' => $data["id"]
			)));
			$quantity = $cart->quantity;
			$now = getdate();
			$expires_date = $cart->expires->sec;
			if(!empty($cart)){
				Cart::remove(array('_id' => $data["id"]));
				#calculate savings
				if($now[0] < $cart->expires->sec) {
					$item[$cart->item_id] = $quantity;
					Cart::updateSavings($item, 'remove');
					$this->addIncompletePurchase(Cart::active());
				}
			}
		}
	}

	/**
	* The update method allow to update the actual cart
	* By refreshing also credits, promocodes, and services
	* @see app/models/Cart::check()
	*/
	public function update() {
		$data = $this->request->data;
		if(!empty($data['rmv_item_id'])) {
			#Removing one item from cart
			$this->remove($data['rmv_item_id']);
		} else if(!empty($data['cart'])) {
			$carts = $data['cart'];
			foreach ($carts as $id => $quantity) {
				$result = Cart::check((integer) $quantity, (string) $id);
				$cart = Cart::find('first', array(
					'conditions' => array('_id' =>  (string) $id)
				));
				if ($result['status']) {
					if($quantity == 0){
				        Cart::remove(array('_id' => $id));
				        $this->addIncompletePurchase(Cart::active());
				    } else {
						$cart->quantity = (integer) $quantity;
						$cart->save();
						$this->addIncompletePurchase(Cart::active());
						$items[$cart->item_id] = $quantity;
						#Check Cart and Refresh Timer
						$this->refreshTimer();
					}
				} else {
					$cart->error = $result['errors'];
					$cart->save();
					$this->addIncompletePurchase(Cart::active());
				}
			}
			#update savings
			Cart::updateSavings($items, 'update');
		}
	}
	
	/**
	* Refresh the timer for each timer in the cart 
	* @see app/models/Cart::check()
	*/
	public function refreshTimer() {
		$actual_cart = Cart::active();
		if (!empty($actual_cart)) {
			$items = $actual_cart->data();
		}
		#T - Refresh the counter of each timer to 15 min
		if (!empty($items)) {
			//Security Check - Max 25 items
			if(count($items) < 25) {
				foreach ($items as $item) {
					$event = Event::find('first',array('conditions' => array("_id" => $item['event'][0])));
					$now = getdate();
					if(($event->end_date->sec > ($now[0] + (15*60)))) {
						$cart_temp = Cart::find('first', array(
							'conditions' => array('_id' =>  $item['_id'])));
						$cart_temp->expires = new MongoDate($now[0] + (15 * 60));
						$cart_temp->save();
					}
				}
			} else {
				#Reset Savings on Session
				Session::write('userSavings', 0);
			}
		}
	}
	
	public function modal(){
	    $userinfo = Session::read('userLogin');
	    $success = true;
	    $this->_render['layout'] = false;
	    if(!array_key_exists('modal', $userinfo)){
	        if($this->request->data){
                $data = $this->request->data;
                $userinfo['modal'] ="disney";
	        }
	        Session::write('userLogin', $userinfo, array('name' => 'default'));
	        $success = false;
	    }
	    echo json_encode($success);
	}
	
	public function upsell(){
        $query = $this->request->query;
        $this->_render['layout'] = 'base';
        if($query){
            $last = strrpos($query['redirect'], '/');
            $url = substr($query['redirect'], 0,$last);
            $total_left = 45 - $query['subtotal'];
            return compact('total_left', 'url');
        }
	}
	
	protected function addIncompletePurchase($items){
		

		if (is_object($items)) $items = $items->data();
		

		
		$user = Session::read('userLogin');
		$base_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$itemToSend = array();
		foreach ($items as $item){
			$eventInfo = Event::find($item['event'][0]);
			if (is_object($eventInfo)) $eventInfo = $eventInfo->data();
			$itemToSend[] = array(
				'id' => $item['_id'],
				'qty' => $item['quantity'],
				'title' => $item['description'],
				'price' => $item['sale_retail']*100,
			 	'url' => $base_url.'sale/'.$eventInfo['url'].'/'.$item['url']
			);

			unset($eventInfo);
		}		
		Mailer::purchase(
			$user['email'],
			$itemToSend,
			array(
				'incomplete' => 1,
				'message_id' => hash('sha256',Session::key('default').substr(strrev( (string) $user['_id']),0,8))
			)
		);
		unset($itemToSend,$user);
	}
}
?>