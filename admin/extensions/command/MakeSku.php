<?php

namespace admin\extensions\command;
use admin\models\Item;
use lithium\analysis\Logger;
use lithium\core\Environment;

class MakeSku extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		$start = time();
		Environment::set($this->env);
		$itemCollection = Item::connection()->connection->items;
		$conditions = array("skus" => array('$exists' => false));
		$items = $itemCollection->find($conditions);
		$i = 0;
		foreach ($items as $item) {
			$i++;
			$skulist = array();
			$hashBySha = false;
			if (!empty($item['details'])) {
				foreach ($item['details'] as $key => $value) {
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
						if ($item["event"][0] == $item_test["event"][0]) {
							if (($item["vendor_style"] != $item_test["vendor_style"]) || ($item["color"] != $item_test["color"])) {
								$hashBySha = true;
							}
						}
					}
				}
				if (!empty($hashBySha)) {
					foreach ($item['details'] as $key => $value) {
						$skulist[$key] = Item::sku($item['vendor'], $item['vendor_style'], $key, $item['color'],'sha256');
					}
				}
				$itemCollection->update(
					array('_id' => $item['_id']),
					array('$set' => array('sku_details' => $skulist)),
					array('upsert' => true)
				);
				$itemCollection->update(
					array('_id' => $item['_id']),
					array('$set' => array('skus' => array_values($skulist))),
					array('upsert' => true)
				);
			}
		}
		$end = time();
		$time = $end - $start;
		$this->out("We updated $i items in $time seconds!");
	}
}