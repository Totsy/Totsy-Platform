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
		if (!empty($this->request->data))
			$this->update();
		#Get current Discount
		$vars = $this->getDiscount();
		//Cart::increaseExpires();
		$cart = Cart::active();
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
		//var_dump($userSavings);
		$savings = $userSavings['items'] + $userSavings['discount'] + $userSavings['services'];
		$postDiscount = ($subTotal + $vars['services']['tenOffFitfy']);
		if(Session::read('credit')) {
			$credits = Session::read('credit');
		 	$postDiscount -= $credits;
		}
		if(!empty($vars['cartPromo']['saved_amount'])) {
		 	$postDiscount += $vars['cartPromo']['saved_amount'];
			$promocode = Session::read('promocode');
		}
		if(!empty($vars['services']['tenOffFitfy'])) {
			$postDiscount -= $vars['services']['tenOffFitfy'];
		}
		if(!empty($vars['cartPromo']['type'])) {
			if($vars['cartPromo']['type'] === 'free_shipping') {
				$shipping_discount = $shipping;
				$promocode = Session::read('promocode');
			}
		}
		if(!empty($vars['services']['freeshipping']['enable'])) {
			$shipping_discount = $shipping;
		}
		$total = ($postDiscount + $shipping - $shipping_discount);
		return $vars + compact('cart', 'message', 'subTotal', 'total', 'shipDate', 'returnUrl', 'promocode', 'savings','shipping_discount', 'credits', 'userDoc','cartItemEventEndDates', 'cartExpirationDate');
	}

	/**
	 * The add method increments the quantity of one item.
	 *
	 * @see app/models/Cart::checkCartItem()
	 * @return compact
	 */
	public function add() {
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
						$cart_temp->expires = new MongoDate($now[0] + (1*60));
						$cart_temp->save();
					}
				}
			}
		} else {
			#Reset Savings on Session
			Session::write('userSavings', 0);
		}
		#T
		$cart = Cart::create();
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
						Cart::updateSavings($item,'add');
					} else {
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
					Cart::updateSavings($item, 'add');
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
				    } else {
						$cart->quantity = (integer) $quantity;
						$cart->save();
						$items[$cart->item_id] = $quantity;
					}
				} else {
					$cart->error = $result['errors'];
					$cart->save();
				}
				
			}
			#update savings
			Cart::updateSavings($items, 'update');
		}
	}
	
	/**
	* The getDiscount metho check credits, promocodes and services available 
	* @see app/models/Cart::check()
	*/
	public function getDiscount() {
		#Initialize datas
		$shippingCost = 7.95;
		$overShippingCost = 0;
		#Get User Infos
		$fields = array(
		'item_id',
		'color',
		'category',
		'description',
		'product_weight',
		'quantity',
		'sale_retail',
		'size',
		'url',
		'primary_image',
		'expires',
		'event',
		'discount_exempt'
		);
		$user = Session::read('userLogin');
		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));
		$cart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		#Get Subtotal
		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());
		/** Services, Promocodes,Credits Management **/
		#Apply Services
		$services = array();
		$services['freeshipping'] = Service::freeShippingCheck();
		$services['tenOffFitfy'] = Service::tenOffFiftyCheck($subTotal);
		#Apply Credits
		$credit_amount = null;
		$cartCredit = Credit::create();
		if (array_key_exists('credit_amount', $this->request->data)) {
			$credit_amount = $this->request->data['credit_amount'];
		}
		$cartCredit->checkCredit($credit_amount, $subTotal, $userDoc);	
		#Apply Promocodes
		$cartPromo = Promotion::create();
		$promo_code = null;
		if (Session::read('promocode')) {
			$promo_session = Session::read('promocode');
			$promo_code = $promo_session['code'];
		}
		if (!empty($this->request->data['code'])) {
			$promo_code = $this->request->data['code'];
		}
		if (!empty($promo_code)) {
			$postDiscountTotal = ($subTotal + $services['tenOffFitfy'] + $cartCredit->credit_amount +  $shippingCost + $overShippingCost);
			$cartPromo->promoCheck($promo_code, $userDoc, compact('postDiscountTotal', 'shippingCost', 'overShippingCost', 'services'));	
		}
		return compact('cartPromo', 'cartCredit', 'services');
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
}
?>