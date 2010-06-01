<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \MongoId;


/**
 * @todo Optimize method addressUpdate so it can be used to add and remove an address
 *	Make sure that when adding or removing an address that we $inc (+/-) the counter.
 *  Currently we only allowing 10 addresses to be stored in MongoDB. This will be hard coded
 *  until we implement a configuration setting.
 */

class User extends \lithium\data\Model {
		
	/**
	 * Update user information in Mongo
	 * 
	 * @todo Fix the long Mongo connection
	 * @param array $data
	 */
	public function update(array $data) {
		$sucess = false;
		$email = Session::read('email');
		
		var_dump($data);
		$sucess = User::_connection()->connection->totsy->users->update(array('email' => "$email" ), array('$set' => $data));

		return $sucess;

	}
	

	
	
	
	
	
}


?>