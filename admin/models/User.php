<?php

namespace admin\models;

use \lithium\data\Connections;
use \lithium\storage\Session;



class User extends \lithium\data\Model {

	public static function applyCredit($invitecode, $credit) {
		$user = User::find('first', array(
			'conditions' => array(
				'invitation_codes' => array($invitecode)
		)));
		$user->total_credit = $user->total_credit + $credit;
		return $user->save(null, array('validate' => false));
	}
}


?>