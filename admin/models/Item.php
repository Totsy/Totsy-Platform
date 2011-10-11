<?php

namespace admin\models;

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

 * }}}
 */
class Item extends \lithium\data\Model {

	protected $_floats = array(
		'msrp',
		'sale_retail',
		'orig_whol',
		'sale_whol',
		'imu',
		'product_weight',
		'shipping_weight',
		'shipping_rate'
		);

	protected $_ints = array(
		'total_quantity'
		);

	protected $_booleans = array(
		'enabled',
		'taxable',
		'shipping_exempt',
		'discount_exempt',
		'shipping_overweight'
		);

	public static function collection() {
		return static::_connection()->connection->items;
	}

	public static function getDepartments() {
		return static::_connection()->connection->command(array('distinct'=>'items', 'key'=>'departments'));
	}
	public static function castData($items, array $options = array()) {

		foreach ($items as $key => $value) {
			if (in_array($key, static::_object()->_floats)) {
				$items[$key] = (float) $value;
			}
		}

		foreach ($items as $key => $value) {
			if (in_array($key, static::_object()->_ints)) {
				$items[$key] = (int) $value;
			}
			if ($key == 'details') {
				foreach ($value as $size => $quantity) {
					$items['details'][$size] = (int) $quantity;
				}
			}
		}

		foreach ($items as $key => $value) {
			if (in_array($key, static::_object()->_booleans)) {
				$items[$key] = (boolean) $value;
			}
		}
		return $items;
	}

	public static function related($item) {
		return static::all(array('conditions' => array(
			'enabled' => true,
			'description' => "$item->description",
			'color' => array('$ne' => "$item->color"),
			'event' => $item->event[0]
		)));
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

	/**
	 * SKU generator for all items.
	 *
	 * This Totsy specific SKU is a combination of the vendor name, style, size and color.
	 * A MD5 hash is taken of each component and limited to 3 characters. This static method should
	 * be used in any instance where SKUs are produced.
	 *
	 * @param string $vendor - Vendor name
	 * @param string $style - Vendor Style
	 * @param string $size - Size of Item
	 * @param string $color - Color of Item
	 * @param string $hash - Either md5 or sha256
	 */
	public static function sku($vendor, $style, $size, $color, $hash = 'md5') {
		$params = array(
			'vendor' => $vendor,
			'style' => $style,
			'size' => $size,
			'color' => $color
		);
		foreach ($params as $key => $param) {
			if ($key == 'vendor') {
				$param = preg_replace('/[^(\x20-\x7F)]*/','', $param);
				$sku[] = strtoupper(substr($param, 0, 3));
			} else if ($key == 'style') {
				if ($hash == 'sha256') {
					$sku[] = strtoupper(substr(hash('sha256',$param.'Totsy@B6è!A'), 7, 3));
				} else if ($hash == 'md5') {
					$sku[] = strtoupper(substr(md5($param), 0, 3));
				}
			} else {
				$sku[] = strtoupper(substr(md5($param), 0, 3));
			}
		}
		return preg_replace('/\s*/m', '', implode('-', $sku));
	}
}

?>