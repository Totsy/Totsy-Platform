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
class Credit extends \lithium\data\Model {

	/**
	 * Default to $15 credits.
	 *
	 * @todo This should be stored as a configuration setting in the database.
	 */
	const INVITE_CREDIT = 15.00;

	protected $_meta = array('locked' => false);

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
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
	public static function add($credit, array $data = array()) {
		$user = Session::read('userLogin');
		$credit->created = static::dates('now');
		$credit->admin_id = $user['_id'];
		$credit->user_id = (string) $data['user_id'];
		$amount = $data['sign'].$data['amount'];
		$credit->credit_amount = (float) $amount;
		$credit->reason = $data['reason'];
		$credit->description = $data['description'];
	 	return static::_object()->save($credit);
	}

}

?>