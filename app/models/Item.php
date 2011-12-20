<?php

namespace app\models;

use MongoId;
use app\models\Base;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 */
class Item extends Base {

	protected $_meta = array('source' => 'items');







	public static function filter($events_id, $departments = null, $categories = null, $ages = null) {
		$itemsCollection = Item::collection();
		if(!empty($departments)){
			$items = $itemsCollection->find(array('event' => array('$in' => $events_id), 'departments' => array('$in' => array($departments))), array('event' => 1));
		}
		elseif(!empty($categories)){
			$items = $itemsCollection->find(array('event' => array('$in' => $events_id), 'category' => array('$in' => array($categories))), array('event' => 1));
		}

		return $items;
	}





	public static function related($item) {

		$color_and_copy_matches = "";
		$related_items = "";

		$color_and_copy_matches = static::all(Array('conditions' => Array(
			'enabled' => true,
			'description' => $item->description,
			'color' => array('$ne' => $item->color),
			'event' => $item->event[0]
		)));

		if($item->related_items){
			$related_items = static::all(Array('conditions' => Array(
			'_id' => Array('$in' => $item->related_items->data())
		)));

			return array_merge($related_items->data());;
		} else {
			return $color_and_copy_matches->data();
		}
	}

	public static function sizes($item) {
		if (empty($item->details)) {
			return array();
		}
		$sizes = array();

		foreach ($item->details as $key => $val) {
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
	 * Perform the atomic operation of marking an item as sold by size.
	 *
	 * When a customer purchases an item the sale count of the item.size
	 * will be incremented. The available quantity for the item will at the same time be
	 * decremented.
	 */
	public static function sold($_id, $size, $quantity) {
		if (!empty($_id) && ( +$quantity > 0)) {
			$condition = array('_id' => new MongoId($_id));
			$update = array(
				'$inc' => array(
					"sale_details.$size.sale_count" => +$quantity,
					"details.$size" => -$quantity,
					"total_quantity" => -$quantity
			));
			return static::collection()->update($condition, $update);
		}
		return false;
	}
}

?>
