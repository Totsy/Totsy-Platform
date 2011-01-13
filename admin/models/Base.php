<?php

namespace admin\models;
use MongoDate;
use lithium\storage\Session as Session;

class Base extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0,
		'-1min' => -60,
		'-3min' => -180,
		'-5min' => -300,
		'3min' => 180,
		'5min' => 300,
		'15min' => 900
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * This method gives direct access to the MongoDB collection object from any
	 * model that extends the Base Model.
	 * @return object
	 */
	public static function collection() {
		return static::_connection()->connection->{static::_object()->_meta['source']};
	}

	public static function createdBy() {
		$user = Session::read('userLogin');
		return $user['_id'];
	}

}

?>