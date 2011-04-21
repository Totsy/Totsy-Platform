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
			if (!empty($item['details'])) {
				foreach ($item['details'] as $key => $value) {
					$skulist[$key] = Item::sku($item['vendor'], $item['vendor_style'], $key, $item['color']);
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