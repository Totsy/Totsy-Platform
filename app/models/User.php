<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \MongoDate;

class User extends \lithium\data\Model {
	
	protected $_dates = array(
		'now' => 0
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	public static function getUser() {
		$user = Session::read('userLogin');
		return User::find('first', array(
			'conditions' => array(
				'_id' => $user['_id'])
		));	
	}
	/**
	 * Cleans the user document.
	 */
	public static function clean($data) {
		if ($data) {
			$data->legacy = 0;
			unset($user->salt);
		}
	}
	/**
	 * During the password reminder process we need to cleanout
	 * the user document. This method will remove the legacy indicator
	 * and salt that was a part of the old system. The password is also
	 * set to a SHA1().
	 */
	public static function process($user, $password, $ip) {
		unset($user->legacy);
		unset($user->salt);
		$user->password = $password;
		$user->updated = static::dates('now');
		return $user->save();
	}

}


?>