<?php

namespace admin\models;

use MongoRegex;
use MongoDate;
use MongoId;
use Mongo;
use lithium\analysis\Logger;


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
	protected static function sku($vendor, $style, $size, $color, $hash = 'md5') {
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
			$newsku = static::getUniqueSku($item['vendor'], $item['vendor_style'], $size, $item['color']);
			$skus[] = $newsku;
			$sku_details[$size] = $newsku;
		}
	    $itemCollection = Item::connection()->connection->items;
		return $itemCollection->update(
			array('_id' => $_id),
			array('$set' => array('sku_details' => $sku_details,'skus' => array_values($skus) ))
		);
	}

	/**
	* Generates all the skus for given item(s).
	* @param array $items
	**/
	public function generateSku($items) {
	    $itemCollection = Item::connection()->connection->items;
	   // Logger::debug("Going generate skus for {$items->count()} items");
	    foreach ($items as $item) {
	    	Logger::debug("Generating skus for {$item['description']} ({$item['_id']}) from event {$item['event'][0]} :");
			$sku_details = array();
			$skus = array();
			$shacount = 0;
			#get sizes
			$sizes = array_keys($item['details']);
			foreach($sizes as $size) {
				$sku = Item::getUniqueSku($item['vendor'], $item['vendor_style'], $size, $item['color']);
				$sku_details[$size] = $sku;
				$skus[] = $sku;
			}	
			$itemCollection->update(
				array('_id' => $item['_id']),
				array('$set' => array('sku_details' => $sku_details ))
			);
			$itemCollection->update(
				array('_id' => $item['_id']),
				array('$set' => array('skus' => $skus ))
			);
		}
		return true;
	}

	/**
	* Responsible for generating unique skus
	* @param string $vendor
	* @param string $vendor_style
	* @param string $size
	* @param string $color
	**/
	public static function getUniqueSku($vendor, $vendor_style, $size, $color) {
		$skus_record = Base::collections('skus_record');

		$sha_vendor_style = $vendor_style;
		#do a search in the skus_record to see if you can find the sku
		$search = $skus_record->findOne(array('size' => $size, 'vendor_style' => $vendor_style));
		if ($search) {
			Logger::debug("\tSku look up was successful for vendor style {$vendor_style} , size {$size}");
			$sku = $search['sku'];
		} else {
			$sku = static::sku($vendor, $vendor_style, $size, $color);
			Logger::debug("\tGenerating new sku for vendor style {$vendor_style} , size {$size}");
			#while skuExists comes back as true, try to create a unique sku
			while (static::skuExists($vendor_style, $sku)) {
				#if we already tried the sha256 and the sku still isn't unique
				if($shacount >= 1) {
					$sha_vendor_style .= hash($vendor_style, 'sha256');
					$sku = static::sku($vendor, $vendor_style, $size, $color,'sha256');
					++$shacount;
					Logger::debug("\tFirst Sha256 failed to generate unique sku vendor style {$vendor_style} , size {$size}. \n\t Retry Round# {$shacount} using {$vendor_style} as vendor_style");
				} else {
					Logger::debug("\tMD5 version already exists for vendor style {$vendor_style} , size {$size}");
					$sku = static::sku($vendor, $vendor_style, $size, $color,'sha256');
					++$shacount;
				}
			}
			Logger::debug("\tSaving new sku {$sku} in skus records ");
			#record the sku in the skus records collection
			$skus_record->save(array(
				'sku' => $sku,
				'vendor' => $vendor,
				'vendor_style' => $vendor_style,
				'size' => $size,
				'color' => $color
				));
			#return Base source to normal
			Base::collections('bases');
		}
		return $sku;
	}

	/**
	* This checks if the given sku already exists for other items on than the vendor_style given
	* @param string $vendor_style
	* @param string $sku
	* @return boolean true for already exists, otherwise false
	**/
	public static function skuExists($vendor_style, $sku) {
		$skus_record = static::collections('skus_record'); 
		$items = static::collections('items');

		#check if this sku exists already for a different item in skus_record
		$record = $skus_record->findOne(array(
			'vendor_style' => array('$ne' => $vendor_style),
			'sku'=> $sku
		));
		#check if this sku exists already for a different item
		$item_record = $items->findOne(array(
			'vendor_style' => array('$ne' => $vendor_style),
			'skus'=> array('$in' => array($sku))
		));

		if($record || $item_record) {
			return true;
		}

		return false;
	}

	public static function generateskusbyevent($_id, $check = false){
		$items = static::collections('items');
		//query items by eventid
		$eventItems = $items->find( array('event' => $_id))->sort(array('created_date' => 1));

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

	/**
	 * Method to get array of skus out of the array of items for a particular order
	 *
	 * @param array $itms
	 */
	public static function getSkus ($itms){
		$itemsCollection = Item::collection();

		$ids = array();
		$items = array();
		$itemSkus = array();

		foreach($itms as $itm){
			$items[$itm['item_id']] = $itm;
			$ids[] = new MongoId($itm['item_id']);
		}
		$iSkus = $itemsCollection->find(array('_id' => array( '$in' => $ids )));
		unset($ids);
		$iSs = array();
		foreach ($iSkus as $i) {
			$iSs[ (string) $i['_id'] ] = $i;
		}

		foreach ($itms as $itm) {
			// If the SKU does not exist for this item generate it now and update the item document
			if (!isset($iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ])) {
				$sku = static::getUniqueSku(
					$iSs[ $itm['item_id'] ]['vendor'],
					$iSs[ $itm['item_id'] ]['vendor_style'],
					$itm['size'],
					$iSs[ $itm['item_id'] ]['color']
				);

				$iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ] = $sku;

				$skuList = array();
				$skuList[ $itm['size'] ] = $sku;
				$skuList = array_merge($iSs[ $itm['item_id'] ]['sku_details'], $skuList);
				$result = $itemsCollection->update(
					array('_id' => new MongoId($itm['item_id'])),
					array('$set' => array('sku_details' => $skuList,'skus' => array_values($skuList) ))
				);
			} else {
				$sku = $iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ];
			}

			$itemSkus[ $sku ] = $itm;
		}

		unset($iSs);
		unset($items);
		return $itemSkus;
	}

}

?>