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
 * Check if the user has used the second purchase $10 off discount
 * 7 days before the end date of the offer.
 * If not send an email reminder.
 */
class SecondPurchaseReminder extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';

	/**
	 * @todo put back original time
	 */
	public function run() {
		Environment::set($this->env);
		$usersCollection = User::connection()->connection->users;
		$servicesCollection = Service::collection();
		$ordersCollection = Order::collection();
		$idx = 0;
		$now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		#RUNNING
		$off10Service = Service::find('first', array('conditions' => array('name' => '10off50')));
		$conditions = array( 'purchase_count' => 1,
								 'created_date' => array(
									'$gt' => $off10Service['start_date'],
									'$lte' => $off10Service['end_date']
						)
		);
		$users = $usersCollection->find($conditions);
		foreach ($users as $user) {
			$conditions_order = array(
										'user_id' => (string) $user['_id'],
			 							'service' => 'freeshipping'
			);
			$order = $ordersCollection->findOne($conditions_order,array('date_created' => 1));
			if(!empty($order)) {
					$verif_date = $order['date_created']->sec;
					//Follow up email 1 week after first purchase with offer and end date
					$day_target = mktime(0, 0, 0, date("m", $verif_date), date("d", $verif_date) + 23, date("Y", $verif_date));
					if($day_target == $now) {
						Mailer::send('Welcome_10_Off_Reminder', $user['email']);
						$idx++;
					}
			}
		}
		echo $idx.' emails reminders have been sent';
	}
}