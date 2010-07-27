<?php

namespace app\models;

use app\models\Event;
use MongoId;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 */
class Item extends \lithium\data\Model {

	public static function collection() {
		return static::_connection()->connection->items;
	}
	
	public function event($item) {
		if (!is_array($item->event) || !$item->event) {
			return null;
		}
		return Event::first(reset($item->event));
	}

	public function related($item) {
		return static::all(array('conditions' => array(
			'enabled' => true,
			'description' => "$item->description",
			'color' => array('$ne' => "$item->color")
		)));
	}

	public function sizes($item) {
		if (empty($item->details)) {
			return array();
		}
		$sizes = array();

		foreach ($item->details->data() as $key => $val) {
			if ($val) {
				$sizes[] = $key;
			}
		}
		return $sizes;
	}

	public function weight($item, $size, $quantity = 1) {
		// @todo
	}
	
	/**
	 * When a customer adds an item to their cart the 
	 * sale_detail.{itemsize}.reserved_count will be incremented. 
	 * If the customer manually removes an item from the cart or 
	 * they purchase an item then 'dec' should be passed as the value of $type.
	 */
	public static function reserve($_id, $size, $quantity) {
		if (!empty($_id)) {
			$_id = new MongoId($_id);
			return static::collection()->update(array(
				'_id' => $_id),
				 array('$inc' => array("sale_detail.$size.reserved_count" => $quantity))
			);
		}
		return false;
	}
	/**
	 * When a customer purchases an item the sale count of the item.size
	 * will be incremented. The available quantity for the item will at the same time be
	 * decremented.
	 */	
	public static function sold($_id, $size, $quantity) {
		if (!empty($_id) && ( (int) $quantity > 1)) {
			$_id = new MongoId($_id);
			return static::collection()->update(array(
				'_id' => $_id),
				 array('$inc' => array("sale_detail.$size.sale_count" => $quantity)),
				 array('$inc' => array("detail.$size" => -$quantity))
			);
		}
		return false;
	}
}

?>