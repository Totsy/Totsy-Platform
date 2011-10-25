<?php

namespace app\models;

use MongoDate;
use MongoId;
use MongoRegex;

class Promocode extends Base {

	protected $_meta = array('source' => 'promocodes');

	/**
	 * The confirm method checks for an incoming promotion code and confirms that
	 * it is valid to use. The validation for a promotion code will include date range,
	 * quantity, order item type and future rules.
	 *
	 * @param string $code promocode
	 */
	public static function confirmCode($code) {
		$code = new MongoRegex("/^($code)$/i");
		return static::find('first', array(
			'conditions' => array(
				'code' => $code,
				'start_date' => array('$lt' => static::dates('now')),
				'end_date' => array('$gt' => static::dates('now')),
				'parent' => array('$ne' => true),
				'enabled' => true
			)));
	}
	/**
	* Updates the stats of a given promocode
	* @param string $_id id of the promocode
	* @param double $discount discount user received
	* @param double $revenue total of a user's order
	* @return boolean success for update
	**/
	public static function add($_id, $discount, $revenue) {
		$_id = new MongoId($_id);
		$update = array(
			'$inc' => array(
				"times_used" => 1,
				"total_discounts" => $discount,
				"total_revenue" => $revenue
		));
		$promocode = static::find('first', array('conditions' => array('_id' => $_id)));
		if ($promocode->special) {
		    $parent_id = $promocode->parent_id;
		    static::collection()->update(array('_id' => $parent_id), $update);
		}
		return static::collection()->update(array('_id' => $_id), $update);
	}
}

?>