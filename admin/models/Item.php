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
			array('$set' => array('sku_details' => $sku_details,'skus' => array_values($skus) ))
		);
	}


	public static function generateSku($items) {
	    $itemCollection = Item::connection()->connection->items;
	    $i =0;
	   // Logger::debug("Going generate skus for {$items->count()} items");
	    foreach ($items as $item) {
	    	Logger::debug("Generating skus for {$item['description']} ({$item['_id']}) from event {$item['event'][0]} :");
			$i++;
			$skulist = array();
			$hashBySha = false;
			$allskus = false;
			$noTries = 0;
			while(!$allskus) {
				if (!empty($item['details'])) {
					foreach ($item['details'] as $key => $value) {
						Logger::debug("\tGenerating sku for size {$key}");
						$sku = Item::sku($item['vendor'], $item['vendor_style'], $key, $item['color'], 'md5');
						//Check duplicate Skus for the same item
						if (in_array($sku, $skulist)) {
							$sku = Item::sku($item['vendor'], $item['vendor_style'], $key, $item['color'], 'sha256');
						}
						$skulist[$key] = $sku;
					}
					$items_tested = $itemCollection->find(array('skus' => array('$in' => $skulist)));
					if (!empty($items_tested)) {
						foreach ($items_tested as $item_test) {
								if (($item["vendor_style"] != $item_test["vendor_style"]) || ($item["color"] != $item_test["color"])) {
									$hashBySha = true;
								}
						}
					}
					if (!empty($hashBySha)) {
						Logger::debug("\tGenerated sku already exists for a completely different item.  Using sha256 to make it unique");
						foreach ($item['details'] as $key => $value) {
							$skulist[(string)$key] = Item::sku($item['vendor'], $item['vendor_style'], $key, $item['color'],'sha256');
						}
					}
					//makes sure that all item sizes have a sku
					if (count($skulist) != count($item['details'])) {
						++$noTries;
						Logger::debug("\tThe amount of skus did not match the number of sizes");
						Logger::debug("\tItems sizes: " . implode(', ', $item['detail']));
						Logger::debug("\tItems skus sizes generated: " . implode(', ', $skulist));
						if ($noTries == 3) {
							//$this->sendMail($item);
							break;
						}
						$allskus = false;
					} else {
						Logger::debug("\tThe amount of skus does match the number of sizes");
						$allskus = true;
					}
				}//end of if
			} //end of while

			$itemCollection->update(
				array('_id' => $item['_id']),
				array('$set' => array('sku_details' => $skulist,'skus' => array_values($skulist) ))
			);
		}
		//$this->item_count = $i;
		return true;
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
		foreach ($iSkus as $i){
			$iSs[ (string) $i['_id'] ] = $i;
		}

		foreach ($itms as $itm){
			// If the SKU does not exist for this item generate it now and update the item document
			if (!isset($iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ])) {
				$sku = Item::sku(
					$iSs[ $itm['item_id'] ]['vendor'],
					$iSs[ $itm['item_id'] ]['vendor_style'],
					$itm['size'],
					$iSs[ $itm['item_id'] ]['color'],
					'md5');

				// Check for duplicate sku
				$temp = $itemsCollection->find(array(
				        'skus' => array('$in' => array($sku)),
				        'vendor_style' => array('$ne' => $iSs[ $itm['item_id'] ]['vendor_style'])
			    	));
				$count = $temp->count();

				if ($count > 0)
					$sku = Item::sku(
						$iSs[ $itm['item_id'] ]['vendor'],
						$iSs[ $itm['item_id'] ]['vendor_style'],
						$itm['size'],
						$iSs[ $itm['item_id'] ]['color'],
						'sha256');

				$iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ] = $sku;

				$skuList = array();
				$skuList[ $itm['size'] ] = $sku;
				$skuList = array_merge($iSs[ $itm['item_id'] ]['sku_details'], $skuList);
				$result = $itemsCollection->update(
					array('_id' => new MongoId($itm['item_id'])),
					array('$set' => array('sku_details' => $skuList,'skus' => array_values($skuList) ))
				);
			}
			else
				$sku = $iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ];

			$itemSkus[ $sku ] = $itm;
		}

		unset($iSs);
		unset($items);
		return $itemSkus;
	}

}

?>