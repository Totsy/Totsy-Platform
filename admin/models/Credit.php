<?php

namespace admin\models;

use \MongoDate;

class Credit extends \lithium\data\Model {

	const INVITE_CREDIT = 15.00;

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}

	public static function add($credit, $data) {
		$credit->created = static::dates('now');
		$credit->user_id = (string) $data['user_id'];
		$credit->credit_amount = (float) $data['amount'];
		$credit->reason = $data['reason'];
		$credit->description = $data['description'];
	 	return static::_object()->save($credit);
	}
}

?>