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
 * who have been invited by someone else.
 */
class ApplyCredits extends \lithium\console\Command {

	public function run() {
		$this->_credit();
	}

	/**
	 * The _credit method loops through all the outstanding invitations and applies credit if applicable.
	 *
	 * The first step of this process is to find all the invitations that have not already been fulfilled.
	 * With the mongo cursor of invititations, use the inviation array to find the invitee email address.
	 * After finding the user document based on email look for any order that has been fulfilled.
	 * This is the part where we'll need to check order.shipped collection. Apply credits if there is an order.
	 * @todo Change this method to utilize the orders.shipped collection.
	 */
	protected function _credit() {
		$conditions = array('status' => 'Accepted', 'credited' => array('$ne' => true));
		$invitations = Invitation::all($conditions);
		$invitationCount = Invitation::count($conditions);
		$this->out("There are currently $invitationCount outstanding invitations.");
		$totalCredit = 0;
		foreach ($invitations as $invitation) {
			$user = User::findByemail($invitation->email);
			$conditions = array('user_id' => (string) $user['_id']);
			$order = Order::first($conditions);
			if ($order) {
				$this->out("Giving a credit to $invitation->user_id");
				$data = array('user_id' => $invitation->user_id);
				if (Credit::add($data, array('type' => 'Invite')) && User::applyCredit($data, array('type' => 'Invite'))) {
					$invitation->credited = true;
					$invitation->save();
				}
			}
		}
	}
}