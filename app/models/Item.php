<?php

namespace app\models;
use MongoId;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 * {{{
     {
        "_id" : ObjectId("4c409b29ce64e5e175040100"),
        "age" : "",
        "category" : "apparel",
        "color" : "Brown",
        "created_date" : ISODate("2010-07-16T17:47:21.416Z"),
        "description" : "Girl Flare Leg Organic Cotton Yoga Pant",
        "details" : {
            "sub_category" : "",
            "no size" : "",
            "0-6M" : "",
            "0-3M" : "",
            "3-6M" : "",
            "6-12M" : "",
            "12-18M" : "",
            "18-24M" : "2",
            "2" : "0",
            "3/4Y" : "2",
            "5/6Y" : "5"
        },
        "enabled" : true,
        "event" : [
            "4c407f7dce64e5e1757d0000"
        ],
        "imu" : 28.6,
        "msrp" : 28,
        "orig_whol" : 14,
        "percent_off" : "60%",
        "product_dimensions" : "",
        "product_weight" : 0,
        "sale_retail" : 11.2,
        "sale_whol" : 8,
        "shipping_dimensions" : "",
        "shipping_weight" : 0,
        "sku_details" : {
            "sub_category" : "KIW-28A-F0E-ED6",
            "no size" : "KIW-28A-B1C-ED6",
            "0-6M" : "KIW-28A-04A-ED6",
            "0-3M" : "KIW-28A-824-ED6",
            "3-6M" : "KIW-28A-DFC-ED6",
            "6-12M" : "KIW-28A-83C-ED6",
            "12-18M" : "KIW-28A-EEA-ED6",
            "18-24M" : "KIW-28A-BBA-ED6",
            "2" : "KIW-28A-C81-ED6",
            "3/4Y" : "KIW-28A-039-ED6",
            "5/6Y" : "KIW-28A-943-ED6"
        },
        "skus" : [
            "KIW-28A-F0E-ED6",
            "KIW-28A-B1C-ED6",
            "KIW-28A-04A-ED6",
            "KIW-28A-824-ED6",
            "KIW-28A-DFC-ED6",
            "KIW-28A-83C-ED6",
            "KIW-28A-EEA-ED6",
            "KIW-28A-BBA-ED6",
            "KIW-28A-C81-ED6",
            "KIW-28A-039-ED6",
            "KIW-28A-943-ED6"
        ],
        "total_quantity" : 9,
        "url" : "girl-flare-leg-organic-cotton-yoga-pant-brown",
        "vendor" : "Kiwi Industries",
        "vendor_style" : "YOGAB"
    }
}}}
 */
class Item extends Base {

	protected $_meta = array('source' => 'items');
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