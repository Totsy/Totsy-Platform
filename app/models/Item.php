<?php

namespace app\models;

use app\models\Event;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 */
class Item extends \lithium\data\Model {

	public function event($item) {
		if (!is_array($item->event) || !$item->event) {
			return null;
		}
		return Event::first(reset($item->event));
	}

	public function related($item) {
		return static::all(array('conditions' => array(
			'enabled' => 1,
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
}

?>