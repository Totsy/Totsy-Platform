<?php

namespace app\models;

use \MongoDate;

class Promotion extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}

	/**
	 * The confirm method checks the total number of times the promotion code
	 * has been used
	 * @param $code
	 * @param $user
	 */
	public static function confirmCount($code_id, $user) {
		return static::count(array(
			'conditions' => array(
				'code_id' => (string) $code_id,
				'user_id' => $user
		)));
	}
}

?>