<?php

namespace admin\extensions\command;
use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\models\OrderShipped;
use admin\models\Invitation;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;


/**
 * Process payments from Authorize.net based on confirmed shipping log.
 */
class ProcessPayment extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'production';

	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		Environment::set($this->env);
		$this->capture();
	}
	/**
	 * Find all orders that have a the field ship_records but no confirmation
	 * auth Key. These are the orders that have a corresponding ship record(s).
	 *
	 * @todo What happens when we apply credit to someone who didn't come in via
	 * an invitation request? Should an invite document be created?
	 */
	public function capture() {
		$ordersCollection = Order::connection()->connection->orders;
		$orders = $ordersCollection->find(array(
			'ship_records' => array('$exists' => true),
			'auth_confirmation' => array('$exists' => false),
		));
		if ($orders) {
			foreach ($orders as $order) {
				$conditions = array('_id' => $order['user_id']);
				$user = User::find('first', compact('conditions'));
				if (Order::process($order) && $user->purchase_count == 1) {
					if ($user->invited_by) {
						$inviter = User::find('first', array(
							'conditions' => array(
								'invitation_codes' => $user->invited_by
						)));
						if ($inviter) {
							$data = array(
								'user_id' => $inviter->_id,
								'description' => "Invite accepted from: $user->email"
							);
							$options = array('type' => 'Invite');
							if (Credit::add($data, $options) && User::applyCredit($data, $options)) {
								$updateInvite = Invitation::find('first', array(
									'conditions' => array(
										'email' => $user->email,
										'user_id' => $inviter->_id
								)));
								if ($updateInvite) {
									$updateInvite->credited = true;
									$updateInvite->save();
								}
							}
						}
					}
				} else {
					Logger::error("Payment capture failed for $order[order_id]");
				}
			}
		}
	}

}