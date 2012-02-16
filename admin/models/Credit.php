<?php

namespace admin\models;

use MongoDate;
use lithium\storage\Session;
/**
 * The Credit model class that interacts with the credits collection.
 *
 * The Credit model performs form general cleanup before data is
 * added to the credits collection.
 */
class Credit extends Base {

	/**
	 * Default to $15 credits.
	 *
	 * @todo This should be stored as a configuration setting in the database.
	 */
	const INVITE_CREDIT = 15.00;
	protected $_meta = array('locked' => false, 'source' => 'credits');
	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static $reasons = array(
		'Credit Adjustment' => 'Credit Adjustment',
		'Invitation' => 'Invitation'
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * Adds a credit document to the credits collection.
	 *
	 * This collection holds all the discrete credit transactions for a customer.
	 * The method takes the credit object that was created along with key data that will
	 * be saved in the object.
	 *
	 * If this is credit is being applied from an administrator then their _id will be added
	 * to the document as an admin_id.
	 *
	 * @param object $credit This object should be just a shell.
	 * @param array $data This array contains all the data that should be added
	 *     to the $credit object and saved.
	 * @return boolean
	 */
	public static function add(array $data = array(), $options = array()) {

		$credit = static::_object()->create();
		$user = Session::read('userLogin');
		$credit->created = static::dates('now');
		$options['type'] = empty($options['type']) ? null : $options['type'];
		$credit->description = empty($data['description']) ? null : $data['description'];
		$amount = 0;

		if ($user) {
			$credit->admin_id = $user['_id'];
		} else {
			// Set to 0 for li3 commands
			$credit->admin_id = 0;
		}
		
		$amount = $data['sign'].$data['amount'];
		$credit->reason = $data['reason'];
		
		if (!empty($data['event_id']) ) {
			$credit->event_id = $data['event_id'];
		}

		if (!empty($data['order_id'])) {
			$credit->order_number = $data['order_number'];
			$credit->order_id = $data['order_id'];
		}
		if ($options['type'] == 'Invite') {
			$amount = static::INVITE_CREDIT;
			$credit->reason = 'Invitation';
		}
		$credit->user_id = (string) $data['user_id'];
		$credit->credit_amount = (float) $amount;

		return static::_object()->save($credit);
	}

}

?>
