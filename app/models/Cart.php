<?php

namespace app\models;

use app\extensions\Ups;
use lithium\storage\Session;
use app\models\Item;
use MongoDate;
use MongoId;

/**
* The Cart Class.
*
* Controls all the model methods needed to interact with the shopping cart.
* The cart document has a one-to-one relationship with an item. What ties all the carts together
* is the session of the user. They see all their carts together based on their user_id and session.
* When the order is placed all the active carts are embedded into the Order Document as an array.
* Carts have the following document structure in Mongo:
* {{{
*	"_id" : ObjectId("4d6dda33538926843a0026ad"),
*	"category" : "Room Decor",
*	"color" : "White",
*	"created" : "Wed Mar 02 2011 00:48:35 GMT-0500 (EST)",
*	"description" : "Baby Blanket Satin Stars",
*	"discount_exempt" : false,
*	"event" : [
*		"4d6c216f12d4c9d022003c7b"
*	],
*	"expires" : "Wed Mar 02 2011 01:03:35 GMT-0500 (EST)",
*	"item_id" : "4d6d1ae75389264724000aaa",
*	"primary_image" : "4d6d5b2753892691130079f4",
*	"product_weight" : 1,
*	"quantity" : 1,
*	"sale_retail" : 8.45,
*	"session" : "p6cdij91661kvm6v95numaqe26",
*	"size" : "no size",
*	"url" : "baby-blanket-satin-stars-white",
*	"user" : "4cacb5efce64e5a875220a00",
*	"vendor_style" : "A22628H"
*
* }}}
* @see app/models/Order::process()
*/
class Cart extends Base {

	/**
	 * The # of business days to be added to an event to determine the estimated
	 * ship by date. The default is 18 business days.
	 *
	 * @var int
	 **/
	protected $_shipBuffer = 15;

	/**
	 * Any holidays that need to be factored into the estimated ship date calculation.
	 *
	 * @var array
	 */
	protected $_holidays = array();

	const TAX_RATE = 0.08875;

	const TAX_RATE_NYS = 0.04375;

	const TAX_RATE_NJS = 0.07;

	const ORIGIN_ZIP = "08837";

	public $validates = array();

	/**
	 * List of common times we use.
	 * @var array
	 */
	protected $_dates = array(
		'now' => 0,
		'-1min' => -60,
		'-3min' => -180,
		'-5min' => -300,
		'3min' => 180,
		'5min' => 300,
		'15min' => 900
	);

	/**
	 * List of NYC Zip Codes
	 * @todo This should be ripped out when we hook in 3rd party tax check.
	 * @var array
	 */
	protected $_nyczips = array(
		'100',
		'104',
		'111',
		'114',
		'116',
		'11004',
		'11005'
	);

	/**
	 * Returns MongoDB collection object
	 */
	public static function collection() {
		return static::_connection()->connection->carts;
	}

	protected $_nonTaxableCategories = array('apparel');

	/**
	 * @todo Need documentation
	 */
	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * @todo Need documentation
	 */
	public static function addFields($data, array $options = array()) {
		$data->expires = static::dates('15min');
		$data->created = static::dates('now');
		$data->session = Session::key('default');
		$user = Session::read('userLogin');
		$data->user = $user['_id'];
		return static::_object()->save($data);
	}

	/**
	 * @todo Need documentation
	 */
	public static function active($params = null, array $options = array()) {
		$fields = (!empty($params['fields'])) ? $params['fields'] : null;
		$time = (!empty($params['time'])) ? $params['time'] : 'now';
		$user = Session::read('userLogin');
		return static::all(array(
			'conditions' => array(
				'session' => Session::key('default'),
				'expires' => array('$gte' => static::dates($time)),
				'user' => $user['_id']),
			'fields' => $fields,
			'order' => array('expires' => 'ASC')
		));
	}

	/**
	 * @todo Need documentation
	 */
	public static function itemCount() {
		$cart = Cart::active(array('fields' => array('quantity')));
		$cartCount = 0;
		if ($cart) {
			foreach ($cart as $item) {
				$cartCount += $item->quantity;
			}
		}
		return $cartCount;
	}

	/**
	 * Computes the total amount of a cart item, including tax, times quantity.
	 *
	 * @param object $cart
	 * @param object $shipping
	 * @return float
	 */
	public function total($cart, $shipping) {
		$unit = $cart->sale_retail + $cart->tax($shipping);
		return $unit * $cart->quantity;
	}

	/**
	 * Computes the subtotal of a cart item,  without tax.
	 *
	 * @param object $cart
	 * @param object $shipping
	 * @return float
	 */
	public function subTotal($cart) {
		return $cart->sale_retail * $cart->quantity;
	}

	/**
	 * Computes the sales tax for an individual item, based on the shipping destination.
	 *
	 * @param object $cart
	 * @param object $shipping
	 * @return float
	 */
	public function tax($cart, $shipping) {
		$item = Item::first($cart->item_id);
		$tax = 0;
		$zipCheckPartial = in_array(substr($shipping['zip'], 0, 3), $this->_nyczips);
		$zipCheckFull = in_array($shipping['zip'], $this->_nyczips);
		$nysZip = ($zipCheckPartial || $zipCheckFull) ? true : false;
		$nycExempt = ($nysZip && $cart->sale_retail < 110) ? true : false;

		if ($item->taxable != false || $nycExempt) {
			switch ($shipping['state']) {
				case 'NY':
					$tax = ($nysZip) ? static::TAX_RATE : static::TAX_RATE_NYS;
					break;
				case 'NJ':
					$tax = static::TAX_RATE_NJS  ;
					break;
				default:
					$tax =  ($cart->sale_retail < 110) ? 0 : static::TAX_RATE;
					break;
			}
		}
		return ($cart->sale_retail * $cart->quantity) * $tax;
	}

	/**
	 * @todo Need documentation
	 */
	public function weight($cart) {
		$item = Item::first($cart->item_id);
		$weight = $item->shipping_weight ?: $item->product_weight;
		$weight = is_string($weight) ? intval(preg_replace('/[^0-9\.]/', '', $weight)) : $weight;
		return ($weight ?: 1) * $cart->quantity;
	}

	/**
	 * @todo Need documentation
	 */
	public static function shipping($carts, $address) {

		// THIS WORKED, BUT WE'RE GOING TO A FLAT RATE
		// $result = floatval(Ups::estimate(array(
		// 	'weight' => array_sum($carts->weight()),
		// 	'product' => "GND",
		// 	'origin' => static::ORIGIN_ZIP,
		// 	'dest' => $address->zip,
		// 	'rate' => "RDP",
		// 	'container' => "CP",
		// 	'rescom' => "RES"
		// )));
		// $cost =  $result ?: 7.95;
		$cost = 7.95;
		$cartCheck = $carts->data();

		if (count($cartCheck) == 1 && Item::first($cartCheck[0]['item_id'])->shipping_exempt) {
			$cost = 0;
		} else {
		    $exempt = true;
		    foreach ($cartCheck as $item) {
		        if (!Item::first($item['item_id'])->shipping_exempt) {
		            $exempt = false;
		            break;
		        }
		    }

		    if ($exempt) {
		        $cost = 0;
		    }
		}

		if (count($cartCheck) == 1 && !Item::first($cartCheck[0]['item_id'])->shipping_exempt && Item::first($cartCheck[0]['item_id'])->shipping_oversize ) {
			$cost = 0;
		}
		return $cost;
	}

	/**
	 * This gets all in a users cart and add the shipping for all oversized
	 *	items
	 */
	public static function overSizeShipping($cart){
			$items= $cart->data();
			$cost=0;
			foreach($items as $item) {
				$info= Item::find($item['item_id']);
				if(array_key_exists('shipping_oversize', $info->data())){
					$data= $info->data();
					$cost+= $data['shipping_rate'];
				}
			}
			return $cost;
	}

	/**
	 * @todo Need documentation
	 */
	public static function checkCartItem($itemId, $size) {
		return static::find('first', array(
			'conditions' => array(
				'session' => Session::key('default'),
				'item_id' => "$itemId",
				'size' => "$size",
				'expires' => array('$gt' => static::dates('now'))
		)));
	}

	/**
	 * @todo Need documentation
	 */
	public static function reserved($item_id, $size) {
		$total = 0;
		$reserved =  static::find('all', array(
			'conditions' => array(
				'item_id' => $item_id,
				'size' => $size),
			'fields' => array('quantity')
		));
		if ($reserved) {
			$carts = $reserved->data();
			foreach ($carts as $cart) {
				$total = $total + $cart['quantity'];
			}
		}
		return $total;
	}

	/**
	 * @todo Need documentation
	 */
	public static function increaseExpires() {
		$user = Session::read('userLogin');
		$conditions = array(
			'session' => Session::key('default'),
			'expires' => array(
				'$lte' => static::dates('3min'),
				'$gte' => static::dates('now')),
			'user' => $user['_id']
		);
		$cartItems = static::find('all', array('conditions' => $conditions));
		if ($cartItems) {
			foreach ($cartItems as $cart) {
				$cart->expires = static::dates('5min');
				$cart->save();
			}
		}
		return true;
	}

	/**
	* Refresh the timer for each timer in the cart
	* @see app/models/Cart::check()
	*/
	public static function refreshTimer() {
		$actual_cart = Cart::active();
		if (!empty($actual_cart)) {
			$items = $actual_cart->data();
		}
		#Refresh the counter of each timer to 15 min
		if (!empty($items)) {
			#Security Check - Max 25 items
			if(count($items) < 25) {
				foreach ($items as $item) {
					if (is_array($item) && !array_key_exists('event', $item) && !array_key_exists('end_date', $item) ){
						continue;
					}
					$event = Event::find('first',array('conditions' => array("_id" => $item['event'][0])));
					$now = getdate();
					if(($event['end_date']->sec > ($now[0] + (15 * 60)))) {
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

	public function cleanExpiredEventItems() {
		$actual_cart = Cart::active();
		if (!empty($actual_cart)) {
			$items = $actual_cart->data();
		}
		if (!empty($items)) {
			foreach ($items as $item) {
				$event = Event::find('first',array('conditions' => array("_id" => $item['event'][0])));
				$now = getdate();
				if (($event->end_date->sec < $now[0])) {
					static::remove(array('_id' => new MongoId( $item["_id"])));
				}
			}
		}
	}

	/**
	 * Check the quanity of an item and compare it to the request value.
	 *
	 * @param float $quantity
	 * @param string $cart_id
	 * @return boolean
	 */
	public static function check($quantity = null, $cart_id = null){
		$cart = static::find('first', array(
			'conditions' => array(
				'_id' => $cart_id
				)
		));
		$item = Item::find('first', array(
				'conditions' => array(
					'_id' => $cart->item_id
		)));
		if ($item->details->{$cart->size} == 0) {
			$check["status"] = false;
			$check["errors"] = "Sorry we are sold out of the <b>$item->description</b>.";
		}
		if ($quantity > $item->details->{$cart->size}) {
			$check["status"] = false;
			$check["errors"] =  "Sorry you have requested more of the <b>$item->description</b> than what is available.";
		}
		if (empty($check["errors"])){
			$check["status"] = true;
		}
		return $check;
	}

	/**
	 * Calculated estimated ship by date for an order.
	 *
	 * The estimated ship-by-date is calculated based on the last event that closes.
	 * @param object $order
	 * @return string
	 */
	public static function shipDate($cart) {
		$i = 1;
		//$event = static::getLastEvent($cart);
		$shipDate = null;
		
		foreach($cart as $thiscart){
			if($thiscart->miss_christmas){
				$shipDate = "<span class=\"shippingalert\">See delivery alert below</span>";	
			}
			else{
				$shipDate = "on or before 12/23";	
			}
		}
		
		
		
		if (!empty($event)) {
			$shipDate = $event->end_date->sec;
			while($i < static::_object()->_shipBuffer) {
				$day = date('N', $shipDate);
				$date = date('Y-m-d', $shipDate);
				if ($day < 6 && !in_array($date, static::_object()->_holidays)) {
					$i++;
				}
				$shipDate = strtotime($date.' +1 day');
			}
		}
		return $shipDate;
	}

	/**
	 * Return the event that will be the last to close in an order.
	 *
	 * This method is needed to determine what the expected ship date should be.
	 * Based on the business model, if a multi event order will ship together then the
	 * estimated ship date will be determined from the fulfillment of the last event.
	 * @param object $order
	 * @return object $event
	 */
	public static function getLastEvent($cart) {
		$event = null;
		$ids = static::getEventIds($cart);
		if (!empty($ids)) {
			$event = Event::find('first', array(
				'conditions' => array('_id' => $ids),
				'order' => array('created_date' => 'DESC')
			));
		}
		return $event;
	}

	/**
	 * Get all the eventIds that are stored either in an order or cart object and cast to MongoId.
	 * @param object
	 * @return array
	 */
	public static function getEventIds($object) {
		$items = (!empty($object->items)) ? $object->items->data() : $object->data();
		$event = null;
		$ids = array();
		foreach ($items as $item) {
			$itemEvent = (empty($item['event'][0])) ? null : $item['event'][0];
			$eventId = (!empty($item['event_id'])) ? $item['event_id'] : $itemEvent;
			if (!empty($eventId)) {
				//$ids[] = new MongoId("$eventId");
				$ids[] = $eventId;
			}
		}
		return $ids;
	}

	/**
	* This method allows to manage (update/delete/add) the savings of the current order
	*
	*/
	public static function updateSavings($items = null, $action, $amount = null) {
		$savings = array('items' => 0, 'discount' => 0, 'services' => 0);
		if(Session::read('userSavings'))
			$savings = Session::read('userSavings');
		if ($action == "update") {
			$savings['items'] = 0;
			if(!empty($items)) {
				foreach($items as $key => $quantity) {
					$itemInfo = Item::find('first', array('conditions' => array('_id' => new MongoId($key))));
					if(!empty($itemInfo->msrp)){
						$savings['items'] += $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
					}
				}
			}
		} else if($action == "add") {
			if(!empty($items)) {
				foreach($items as $key => $quantity) {
					$itemInfo = Item::find('first', array('conditions' => array('_id' => $key)));
					if(!empty($itemInfo->msrp)){
						$savings['items'] += $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
					}
				}
			}
		} else if($action == "remove") {
			foreach($items as $key => $quantity) {
				$itemInfo = Item::find('first', array('conditions' => array('_id' => $key)));
				if(!empty($itemInfo->msrp)){
					$savings['items'] -= $quantity * ($itemInfo->msrp - $itemInfo->sale_retail);
				}
			}
		} else if ($action == 'discount') {
			$savings['discount'] = $amount;
		} else if ($action == 'services') {
			$savings['services'] = $amount;
		}
		Session::write('userSavings', $savings);
	}

	/**
	* The getDiscount method check credits, promocodes and services available
	* And Apply it to the subtotal
	* Return Credit, Promo, Services Objects and the PostDiscount Total
	* @see app/models/Cart::check()
	*/
	public static function getDiscount($subTotal, $shippingCost = 7.95, $overShippingCost = 0, $data, $tax = 0.00) {
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
		/** Services, Promocodes,Credits Management **/
		#Apply Services
		$services = array();
		$services['freeshipping'] = Service::freeShippingCheck($shippingCost, $overShippingCost);
		$services['tenOffFitfy'] = Service::tenOffFiftyCheck($subTotal);
		#Calculation of the subtotal with shipping and services discount
		$postSubtotal = ($subTotal + $tax + $shippingCost + $overShippingCost - $services['tenOffFitfy'] - $services['freeshipping']['shippingCost'] - $services['freeshipping']['overSizeHandling']);
		$subTotal += $tax;
		#Apply Promocodes
		$cartPromo = Promotion::create();
		$promo_code = null;
		if (Session::read('promocode')) {
			$promo_session = Session::read('promocode');
			$promo_code = $promo_session['code'];
		}
		if (!empty($data['code'])) {
			$promo_code = $data['code'];
		}
		#Disable Promocode Uses if Services
		if (!empty($services['freeshipping']['enable']) || !empty($services['tenOffFitfy'])) {
			$promo_code = null;
		}
		if (!empty($promo_code)) {
			$cartPromo->promoCheck($promo_code, $userDoc, compact('subTotal', 'shippingCost', 'overShippingCost', 'services'));
		}
		#Apply Credits
		$credit_amount = null;
		$cartCredit = Credit::create();
		if (array_key_exists('credit_amount', $data)) {
			$credit_amount = $data['credit_amount'];
		}
		$postDiscountTotal = ($postSubtotal + $cartPromo['saved_amount']);
		#Avoid Negative Total
		if($postDiscountTotal < 0.00) {
			$postDiscountTotal = 0.00;
		}
		$cartCredit->checkCredit($credit_amount, $subTotal, $userDoc);
		#Apply credit to the Total
		if(!empty($cartCredit->credit_amount)) {
			$postDiscountTotal += $cartCredit->credit_amount;
		}
		return compact('cartPromo', 'cartCredit', 'services', 'postDiscountTotal');
	}
}

?>
