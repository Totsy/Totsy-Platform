<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\extensions\Mailer;
use admin\models\Order;
use admin\models\User;
use MongoDate;

/**
 * This command resends emails to customer based on the available choices.
 * For example, during 12/27/2011 after 7pm user weren't receiving their order
 * confirmation and there was no log in Sailthru that says the email went out
 */
class ResendEmail extends Base {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	/**
	 * Email address to redirect the emails to during test mode.
	 *
	 * @var string
	 */
	public 	$email = null;
	/**
	 * Sailthru Template to use.
	 *
	 * @var string
	 */
	public $template = null;

	/**
	* Availabe choices to query by and send an email to.
	* @var string
	*/
	public $sendtype = 'order_confirmation';

	/**
	 * Testing mode default to false
	 * @var bool
	 */
	public $test = false;

	/**
	 * Sends the first five emails during test mode
	 * @var bool
	 */
	public $sendcount = 5;

	/**
	 * Minimum datetime range to retrieve.
	 * Format: 12/28/2011 00:00:00
	 * @var string
	 */
	public $mindatetime = null;

	/**
	 * Maximum datetime range to retrieve
	 * Format: 12/28/2011 23:59:59
	 * @var string
	 */
	public $maxdatetime = null;

	/**
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);

		if (!$this->mindatetime) {
			echo "min datetime not set.  Please set\n";
			exit(1);
		}
		if (!$this->maxdatetime) {
			echo "max datetime not set.  Please set\n";
			exit(1);
		}

		switch($this->sendtype) {
			case "order_confirmation":
				$this->resendOrderConfirmation();
				break;
		}

	}

	protected function resendOrderConfirmation() {
		if (!$this->template){
			$this->template = "Order_Confirmation";
		}

		$orderColl = Order::collection();

		$orders = $orderColl->find(array("date_created" => array(
			'$gte' => new MongoDate(strtotime($this->mindatetime)),
			'$lte' => new MongoDate(strtotime($this->maxdatetime))
		)));

		$count = $this->sendcount;
		foreach($orders as $order) {
			$data = array(
				'order' => $order,
				'shipDate' => date('m-d-Y', $order['ship_date']->sec)
			);
			$user = User::lookUp($order['user_id']);
			$email = $user->email;

			if ((bool)$this->test) {
				$email = $this->email;
			}

			if ($count == 0) {

				exit(0);
			}

			Mailer::send($this->template, $email, $data);
			--$count;
		}

	}
}