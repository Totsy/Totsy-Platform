<?php

namespace admin\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use MongoRegex;


class User extends \lithium\data\Model {

	public static function applyCredit($data) {
		$user = User::find('first', array(
			'conditions' => array(
				'_id' => $data['user_id']
		)));
		$user->total_credit = $user->total_credit + $data['amount'];
		return $user->save(null,array('validate' => false));
	}

	public static function findUsers($data) {
		$exclude = array('address_type', 'type');
		foreach ($data as $key => $value) {
			if (($value != '') && (!in_array($key, $exclude))) {
				$conditions["$key"] = new MongoRegex("/$value/i");
			}
		}
		return static::find('all', array('conditions' => $conditions));
	}
}


?>