<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Credit;
use admin\models\Order;
use admin\models\User;
use admin\models\Invitation;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * The credits command is executed to provide invitation credits to all users
 * who have accepted an invitation and have sucessfully placed an order.
 */
class ApplyCredits extends \lithium\console\Command {

	public function run() {
		$this->header('Launching search for Invitations....');
		$this->_credit();
		$this->out('Finished Applying Credits');
	}

	/**
	 * The _credit method loops through all the outstanding invitations and applies credit if applicable.
	 *
	 * The first step of this process is to find all the invitations that have not already recieved credit.
	 * With the mongo cursor of invititations, use the inviation array to find the invitee email address.
	 * After finding the user document based on email look for any order that has been fulfilled.
	 * This is the part where we'll need to check order.shipped collection. Apply credits if there is an order.
	 * @todo Change this method to utilize the orders.shipped collection.
	 */
	protected function _credit() {
		$affiliates = User::findAllByaffiliate(true, array('fields' => array('_id' => true)));
		$affiliates = $affiliates->data();
		foreach ($affiliates as $value) {
			$ids[] = (string) $value['_id'];
		}
		$conditions = array(
			'status' => 'Accepted',
			'credited' => array('$ne' => true),
			'user_id' => array('$nin' => $ids)
		);
		$invitations = Invitation::all($conditions);
		$invitationCount = Invitation::count($conditions);
		$this->out("There are currently $invitationCount outstanding invitations.");
		foreach ($invitations as $invitation) {
			$user = User::findByemail($invitation->email);
			$conditions = array(
				'user_id' => (string) $user['_id'],
				'items.status' => array('$ne' => 'Order Canceled'
			));
			$order = Order::first($conditions);
			if ($order) {
				$user = User::find($invitation->user_id);
				$invitationCheck = User::find('first', array(
					'email' => $invitation->email,
					'credited' => true
				));
				if (empty($invitationCheck)) {
					$this->out("Giving a credit to $invitation->user_id");
					$data = array(
						'user_id' => $invitation->user_id,
						'description' => "Invite accepted from: $invitation->email"
					);
					$options = array('type' => 'Invite');
					if (Credit::add($data, $options) && User::applyCredit($data, $options)) {
						$invitation->credited = true;
						$invitation->save();
					}
				}
			}
		}
	}
}