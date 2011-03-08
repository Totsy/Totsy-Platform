<?php

namespace app\models;

use app\extensions\Ups;
use lithium\storage\Session;
use app\models\Item;
use MongoDate;

/**
* The Cart Class.
*
* Controls all the model methods needed to interact with the shopping cart.
* The cart docuemnt has a one-to-one relationship with an item. What ties all the carts together
* is the session of the user. They see all their carts together based on their user_id and session.
* When the order is placed all the active carts are embedded into the Order Document as an array.
* Carts have the following document structure in Mongo:
* {{{
*	"_id" : ObjectId("4d6dda33538926843a0026ad"),
*	"category" : "Room Decor   ",
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
class Cart extends \lithium\data\Model {

	const TAX_RATE = 0.08875;

	const TAX_RATE_NYS = 0.04375;

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
		$zipCheckPartial = in_array(substr($shipping->zip, 0, 3), $this->_nyczips);
		$zipCheckFull = in_array($shipping->zip, $this->_nyczips);
		$nysZip = ($zipCheckPartial || $zipCheckFull) ? true : false;
		$nycExempt = ($nysZip && $cart->sale_retail < 110) ? true : false;

		if ($item->taxable != false || $nycExempt) {
			switch ($shipping->state) {
				case 'NY':
					$tax = ($nysZip) ? static::TAX_RATE : static::TAX_RATE_NYS;
					break;
				default:
					$tax =  ($cart->sale_retail < 110) ? 0 : static::TAX_RATE;
					break;
			}
		}
		return $cart->sale_retail * $tax;
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
		}

		if (count($cartCheck) == 1 && !Item::first($cartCheck[0]['item_id'])->shipping_exempt && Item::first($cartCheck[0]['item_id'])->shipping_oversize ) {
			$cost = 0;
		}
		return $cost;
	}

	/**
	 * @todo Need documentation
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
	 * @todo Need documentation
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
}

?>
