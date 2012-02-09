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
        $this->generateSku($items);
		$end = time();
		$time = $end - $start;
		$this->out("We updated " . $this->item_count . " items in $time seconds!");
	}

	public function generateSku($items) {
	    $itemCollection = Item::connection()->connection->items;
	    $i =0;
	    Logger::debug("Going generate skus for {$items->count()} items");
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
							$this->sendMail($item);
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
		$this->item_count = $i;
	}

	private function sendMail($item){
		$k_implode = function(array $subject, $glue = " " , $separator = ":") {
			if(empty($subject)) return false;
				$string = "";
				foreach($subject as $key => $value) {
					$string .= $key . $separator . $value . $glue;
				}
				return rtrim($string, $glue);
		};

		$sizes = implode(', ', array_keys($item['details']));
		$skus = $k_implode($skulist);
		$diff = implode(', ' , array_keys(array_diff_key($item['details'], $skulist)));
		$To = "bugs@totsy.com, logistics@totsy.com";
		$subject = "Make-sku fail: Potential Sku Problem with item id: {$item['_id']}";
		$header = "From: reports@totsy.com";
		$message = <<<MESSAGE
		There was an problem generating all skus for :
		\tItem Name: {$item['description']} {$item['color']} ({$item['_id']})
		\t\tItem contains the following sizes: {$sizes}
		\t\tItem size-sku pair that generated (size:sku): {$skus}
		\t\tFailed to generate for the following sizes: {$diff}

		Here is the link to the item http://admin.totsy.com/items/edit/{$item['_id']}

		This message was generated after 3 attempts.
MESSAGE;
		mail($To , $subject , $message);
	}
}