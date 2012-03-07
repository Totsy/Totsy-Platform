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
     * Directory of tmp files.
     *
     * @var string
     */
    public $tmp = '/resources/totsy/tmp/';
	private $item_count = 0;

	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		$start = time();
		Environment::set($this->env);
        $itemCollection = Item::connection()->connection->items;
        $conditions = array('$or' => array(
            array('skus' => array('$exists' => false)),
            array('sku_details' => array('$exists' => false ))
        ));
        $items = $itemCollection->find($conditions);
        $makeSkus = new Item();
       	$makeSkus->generateSku($items);
		$end = time();
		$time = $end - $start;
	}
}