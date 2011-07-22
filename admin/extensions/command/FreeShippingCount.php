<?php

namespace admin\extensions\command;
use admin\models\Item;
use admin\models\Promotion;
use admin\models\Order;
use admin\models\User;
use lithium\analysis\Logger;
use lithium\core\Environment;
use MongoDate;
use MongoId;

class FreeShippingCount extends \lithium\console\Command  {

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
		Environment::set($this->env);
		$start_date = mktime(0, 0, 0, 6, 1, 2011);
		$end_date = mktime(0, 0, 0, 6, 30, 2011);
		$usersCollection = User::connection()->connection->users;
		$ordersCollection = Order::Collection();
		$idx = 0;
		$counter = 0;
		$over = 0;
		#RUNNING
		$conditions = array( 'service' => '10off50',
							 'date_created' => array(
								'$gt' => new MongoDate($start_date),
								'$lte' => new MongoDate($end_date)
					)
		);
		$results = $ordersCollection->find($conditions);
		foreach	($results as $res) {
			$counter ++;
		}
		echo ' Free Shipping Service offers: $ '. ($counter);
		
		
	}

}