<?php

namespace app\models;
use MongoId;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 */
class Item extends \lithium\data\Model {

	public static function collection() {
		return static::_connection()->connection->items;
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
			if ($val && ($val > 0)) {
				$sizes[] = $key;
			}
		}
		return $sizes;
	}

	public function weight($item, $size, $quantity = 1) {
		// @todo
	}

	/**
	 * Perform the atomic operation on item quantities by size.
	 *
	 * Only reserve the quantity if the requested amount is less than or equal
	 * to the actual quantity.
	 */
	public static function reserve($_id, $size, $quantity) {
		if (!empty($_id)) {
			$_id = new MongoId($_id);
			return static::collection()->update(array(
				'_id' => $_id, "details.$size" => array('$gte' => $quantity)),
				 array('$inc' => array("sale_details.$size.reserved_count" => $quantity))
			);
		}
		return false;
	}
	/**
	 * Perform the atomic operation of marking an item as sold by size.
	 *
	 * When a customer purchases an item the sale count of the item.size
	 * will be incremented. The available quantity for the item will at the same time be
	 * decremented.
	 */	
	public static function sold($_id, $size, $quantity) {
		if (!empty($_id) && ( +$quantity > 1)) {
			$_id = new MongoId($_id);
			return static::collection()->update(array(
				'_id' => $_id, "details.$size" => array('$gte' => +$quantity)),
				 array(
					'$inc' => array(
						"sale_details.$size.sale_count" => +$quantity,
						"details.$size" => -$quantity,
						"sale_details.$size.reserved_count" => -$quantity,
						"total_quantity" => -$quantity
			)));
		}
		return false;
	}
}

?>