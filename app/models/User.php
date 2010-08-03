<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \MongoDate;
use \MongoId;
use \MongoRegex;

class User extends \lithium\data\Model {
	

	protected $_dates = array(
		'now' => 0
	);

	public static function collection() {
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
	 * Lookup a user by either their email or username
	 */
	public static function lookup($email) {
		$user = null;
		$email = new MongoRegex("/$email/i");
		$result = static::collection()->findOne(array(
			'$or' => array(array('username' => $email), array('email' => $email)))
		);
		if ($result) {
			$user = User::create($result);
		}
		return $user;
	}

	public static function log($ipaddress) {
		$user = static::getUser();
		++$user->logincounter;
		$data = array(
			'lastip' => $ipaddress,
			'lastlogin' => static::dates('now')
		);
		return $user->save($data);
	}

	public static function applyCredit($user_id, $credit) {
		$user = User::find('first', array(
			'conditions' => array(
				'_id' => $user_id
		)));
		$user->total_credit = $user->total_credit + $credit;
		return $user->save();
	}

}


?>