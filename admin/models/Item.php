<?php

namespace admin\models;

use admin\models\ItemImage;
use lithium\util\String;

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
	public static function calculateProductGross($items) {
		if (empty($items)) return 0;

		$gross = 0;
		foreach($items as $item) {
			$cancel = array_key_exists('cancel' , $item) && !$item['cancel'];
			$cancel = $cancel || !array_key_exists('cancel' , $item);
			if ($cancel) {
				$gross += $item['quantity'] * $item['sale_retail'];
			}
		}
		return $gross;
	}
	/* Handling of attached images. */

	public function attachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = ItemImage::$types[$name];

		if ($type['multiple']) {
			$images = $entity->{$type['field']} ? $entity->{$type['field']}->data() : array();

			if (!in_array($id, $images)) {
				$images[] = $id;
			}
			$entity->{$type['field']} = $images;
		} else {
			$entity->{$type['field']} = $id;
		}
		return $entity;
	}

	public function detachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = ItemImage::$types[$name];

		if ($type['multiple']) {
			$images = $entity->{$type['field']}->data();

			if ($key = array_search($id, $images)) {
				unset($images[$key]);
			}
			$entity->{$type['field']} = $images;
		} else {
			$entity->{$type['field']} = null;
		}
		return $entity;
	}
	public function images($entity) {
		$results = array();

		foreach (ItemImage::$types as $name => $type) {
			$results[$name] = $type['multiple'] ? array() : null;

			if (!$entity->{$type['field']}) {
				continue;
			}
			if ($type['multiple']) {
				foreach ($entity->{$type['field']} as $key => $value) {
					$results[$name][$key] = ItemImage::first(array(
						'conditions' => array('_id' => $value)
					));
				}
			} else {
				$results[$name] = ItemImage::first(array(
					'conditions' => array('_id' => $entity->{$type['field']})
				));
			}
		}
		return $results;
	}

	public function uploadNames($entity) {
		$results = array();

		foreach (ItemImage::$types as $name => $type) {
			$results['form'][$name] = String::insert($type['uploadName']['form'], array(
				'url' => $entity->url,
				'name' => $name
			));
		}
		return $results;
	}
}

?>