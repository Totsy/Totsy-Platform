<?php

namespace admin\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use MongoRegex;


class User extends \lithium\data\Model {

	public static function applyCredit($invitecode, $credit) {
		$user = User::find('first', array(
			'conditions' => array(
				'invitation_codes' => array($invitecode)
		)));
		$user->total_credit = $user->total_credit + $credit;
		return $user->save(null, array('validate' => false));
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