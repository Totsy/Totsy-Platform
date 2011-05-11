<?php

namespace admin\extensions\command;

use admin\models\User;
use admin\models\Order;
use admin\models\Service;
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
		$servicesCollection = Service::collection();
		$now = new MongoDate();
		#OPTIMIZATION
		$usersCollection->ensureIndex(array('created_date' => 1));
		$usersCollection->ensureIndex(array('purchase_count' => 1));
		#RUNNING
		$freeshipService = $servicesCollection->findOne(array('name' => 'Free Shipping'));
		
		$creation_date = $freeshipService['start_date'];
		$conditions = array( 'purchase_count' => array('$exists' => false),
							 'created_date' => array(
					           '$gt' => $creation_date
					)
		);
		$usersCollection->find($conditions);
	}
}