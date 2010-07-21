<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \MongoDate;

class User extends \lithium\data\Model {
	

	protected $_dates = array(
		'now' => 0
	);

	public function collection() {
		return static::_connection()->connection->users;
	}

	public static function push($field, $data)
	{
		$user = static::getUser();
		return static::collection()->update(array(
			'_id' => $user->_id),
			 array('$pushAll' => array($field => $data))
		);
	}
	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	public static function getUser($fields = null) {
		$user = Session::read('userLogin');
		return User::find('first', array(
			'conditions' => array(
				'_id' => $user['_id']),
			'fields' => $fields
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

	public static function invite($to, $message) {
		$user = null;
		foreach ($to as $value) {
			$data[] = array(
				'date_sent' => static::dates('now'),
				'email' => $value,
				'status' => 'unused'
			);
		}
		if (static::push('invitations', $data)) {
			$user = static::getUser(array('invitations', 'invite_code'));
		}

		return	$user;
	}
	/**
	 * Lookup a user by either their email or username
	 */
	public static function lookup($identity) {
		$result = static::collection()->find(array(
			'$or' => array(array('username' => "$identity", 'email' => "$identity")))
		);
		$array = iterator_to_array($result);
		return User::create($array);
	}
}


?>