<?php

namespace admin\extensions\command;

use admin\models\User;
use admin\models\Order;

/**
 *
 */
class FirstPurchaseReminder extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';

	/**
	 * 
	 */
	public function run() {
		$usersCollection = User::collection();
		#OPTIMIZATION
		$usersCollection->ensureIndex(array('created_date' => 1));
		$usersCollection->ensureIndex(array('purchase_count' => 1));
		
		
		$creation_date = mktime(0, 0, 0, date("m")-11, 1, date("Y")); 
		$conditions_user_V1 = array(
		         'created_date' => array(
		           '$gt' => new MongoDate($creation_date)
		));

		$conditions = array( 'purchase_count' => array('$exists' => false),
							 ''
		)
	}
}