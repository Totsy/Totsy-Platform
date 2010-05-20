<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;

class User extends \lithium\data\Model {
	
	/**
	 * Update user information in Mongo
	 * 
	 * @todo Fix the long Mongo connection
	 * @param array $data
	 */
	public function update(array $data) {
		$sucess = false;
				
		$db = Connections::get('default');
		$email = Session::read('email');
		$sucess = $db->connection->totsy->users->update(array('email' => "$email" ), array('$set' => $data));

		return $sucess;

	}
}


?>