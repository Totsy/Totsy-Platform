<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;


class User extends \lithium\data\Model {
	
	public static function getUser() {

		$user = Session::read('userLogin');
		return User::find('first', array(
			'conditions' => array(
				'_id' => $user['_id'])
		));	
	}

}


?>