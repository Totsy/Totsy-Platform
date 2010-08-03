<?php

namespace app\models;

use app\extensions\Ups;
use lithium\storage\Session;
use MongoDate;

class Cart extends \lithium\data\Model {

	const TAX_RATE = 0.08875;

	public $validates = array();
	
	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	protected $_nonTaxableCategories = array('apparel');

	public static function dates($name) { 
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}

	public static function addFields($data, array $options = array()) {

		$data->expires = static::dates('tenMinutes');
		$data->created = static::dates('now');
		$data->session = Session::key();
		$user = Session::read('userLogin');
		$data->user = $user['_id'];
		return static::_object()->save($data);
	}
	
	public static function active($params = null, array $options = array()) {
		$fields = $params['fields'];
		$user = Session::read('userLogin');
		return static::all(array(
			'conditions' => array(
				'session' => Session::key(),
				'expires' => array('$gt' => static::dates('now')),
				'user' => $user['_id']),
			'fields' => $fields,
			'order' => array('expires' => 'ASC') 
		));
	}

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
		$taxExempt = (
			$shipping->state != 'NY' ||
			($item->taxable && $cart->sale_retail < 110)
		);

		if ($taxExempt) {
			return 0;
		}
		return $cart->sale_retail * static::TAX_RATE;
	}

	public function weight($cart) {
		$item = Item::first($cart->item_id);
		return intval(preg_replace('/[^0-9\.]/', '', $item->product_weight)) * $cart->quantity;
	}

	public static function shipping($carts, $address) {
		return floatval(Ups::estimate(array(
			'weight' => array_sum($carts->weight()),
			'product' => "GND",
			'origin' => "18106",
			'dest' => $address->zip,
			'rate' => "RDP",
			'container' => "CP",
			'rescom' => "RES"
		)));
	}

	public static function checkCartItem($itemId, $size) {
		return static::find('first', array(
			'conditions' => array(
				'session' => Session::key(),
				'item_id' => "$itemId",
				'size' => "$size",
				'expires' => array('$gt' => static::dates('now'))
		)));
	}

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
}

?>