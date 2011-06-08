<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\extensions\command\DisneyExport;
use admin\models\Order;
use admin\models\User;
use admin\models\Disney;
use MongoId;
use MongoDate;


/**
 * Make a check for 0 records on the Disney Collection.
 * Redo the disney export at this specific date if it's the case
 */
class DisneyCheck extends \lithium\console\Command {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	
	/**
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);
		//Get All the disney records
		$logs = Disney::collection()->find(array('records' => 0));
		foreach ($logs as $log) {
				//Get the specific date
				$month = substr($log['file'], 3, 2);
				$day = substr($log['file'], 5, 2);
				//Redo the disney export
				$Disney = new DisneyExport();
				$Disney->startDay = $day;
				$Disney->startMonth = $month;
				$Disney->run();
				//Delete zero Records in DB
				Disney::collection()->remove(array('_id' => $log['_id']));
		}
	}
}