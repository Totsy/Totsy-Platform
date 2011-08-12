<?php

namespace app\models;

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

	public static function add($credit, $user_id, $amount, $reason, $orderid) {
		$credit->created = static::dates('now');
		$credit->user_id = (string) $user_id;
		$credit->credit_amount = $amount;
		$credit->order_id = $orderid;
		$credit->reason = $reason;
		return static::_object()->save($credit);
	}
}

?>