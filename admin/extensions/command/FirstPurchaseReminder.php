<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\User;
use admin\models\Order;
use admin\models\Service;
use MongoDate;
use MongoId;
use admin\extensions\Mailer;

/**
 * Check if the user has used the first purchase free shipping discount
 * 7 days before the end date of the offer.
 * If not send an email reminder.
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
		Environment::set($this->env);
		$usersCollection = User::connection()->connection->users;
		$servicesCollection = Service::collection();
		$idx = 0;
		#RUNNING
		$freeshipService = Service::find('first', array('conditions' => array('name' => 'freeshipping')));
		$conditions = array( 'purchase_count' => array('$exists' => false),
							 'created_date' => array(
								'$gt' => $freeshipService['start_date'],
								'$lte' => $freeshipService['end_date']
					)
		);
		$users = $usersCollection->find($conditions);
		$now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		foreach ($users as $user) {
			$verif_date = $user['created_date']->sec;
			$day_target = mktime(0, 0, 0, date("m", $verif_date), date("d", $verif_date) + 23, date("Y", $verif_date));
			if($day_target == $now) {
				Mailer::send('Welcome_Free_Shipping_Reminder', $user['email']);
				$idx++;
			}
		}
		echo $idx.' emails reminders have been sent';
	}
}