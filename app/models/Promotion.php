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
	 * has been used by an individual user
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
	/**
	 * The confirm no of uses method checks the TOTAL number of times the promotion code
	 * has been used
	 * @param $code
	 */
	public static function confirmNoUses($code_id, $user) {
		return static::count(array(
			'conditions' => array(
				'code_id' => (string) $code_id,
				'user_id' => array('$ne' => $user)
		)));
	}
}

?>