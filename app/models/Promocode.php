<?php

namespace app\models;

use MongoDate;
use MongoId;
use MongoRegex;

class Promocode extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0
	);

	public static function collection() {
		return static::_connection()->connection->promocodes;
	}

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * The confirm method checks for an incoming promotion code and confirms that
	 * it is valid to use. The validation for a promotion code will include date range,
	 * quantity, order item type and future rules.
	 *
	 */
	public static function confirmCode($code) {
		$code = new MongoRegex("/^($code)$/i");
		return static::find('first', array(
			'conditions' => array(
				'code' => $code,
				'start_date' => array('$lt' => static::dates('now')),
				'end_date' => array('$gt' => static::dates('now')),
				'enabled' => true
			)));
	}
	public static function add($_id, $discount, $revenue) {
		$_id = new MongoId($_id);
		$update = array(
			'$inc' => array(
				"times_used" => 1,
				"total_discounts" => $discount,
				"total_revenue" => $revenue
		));
		return static::collection()->update(array('_id' => $_id), $update);
	}
}

?>