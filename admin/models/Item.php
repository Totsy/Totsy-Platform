<?php

namespace admin\models;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
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
	 * @param string $vendor
	 * @param string $style
	 * @param string $size
	 * @param string $color
	 */
	public static function sku($vendor, $style, $size, $color) {
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
				$sku[] = strtoupper(substr(md5($param), 4, 6));
			} else {
				$sku[] = strtoupper(substr(md5($param), 0, 3));
			}
		}
		return preg_replace('/\s*/m', '', implode('-', $sku));
	}
}

?>