<?php

namespace admin\models;

use MongoRegex;
use MongoDate;
use MongoId;
use Mongo;

/**
 * The `Item` class extends the generic `lithium\data\Model` class to provide
 * access to the Item MongoDB collection. This collection contains all product items.
 */
class Item extends Base {

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
					$items['details'][(string)$size] = (int) $quantity;
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

		foreach ($item->details as $key => $val) {
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

	public static function addskus($_id){
		//query single item
		$item = Item::find('first', array('conditions' => array('_id' => $_id)));

		//new sku array
		$skus = array();
		$sku_details = array();
	
		//loop through sizes and create skus
		foreach($item['details'] as $size => $quantity){
			$newsku = Item::sku($item['vendor'], $item['vendor_style'], $size, $item['color']);
			$skus[] = $newsku;
			$sku_details[$size] = $newsku;
		}

	    $itemCollection = Item::connection()->connection->items;
		return $itemCollection->update(
			array('_id' => $_id),
			array('$set' => array('sku_details' => $sku_details,'skus' => array_values($skus) )),
			array('upsert' => true)
		);
	}

	
	public static function generateskusbyevent($_id, $check = false){
		//query items by eventid
		$eventItems = Item::find('all', array('conditions' => array('event' => $_id),
				'order' => array('created_date' => 'ASC')
			));

		//loop through items
		foreach($eventItems as $item){
			//check for existing skus?
			if($check){
				if(count($item['details'])!=count($item['skus'])){
					$addsku .= Item::addskus($item['_id']);
				}
			}
			//just replace all skus
			else{
				$addsku .= Item::addskus($item['_id']);
			}
		}
		return $addsku;
	}


}

?>