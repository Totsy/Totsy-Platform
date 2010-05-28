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
	 * This is the maximum number of addresses that can be stored.
	 * @var int 
	 */
	public static $_maxAddress = 10;
	
	
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
	
	public function addressUpdate(array $data) {
		$success = false;
		$message = "";
		$collection = User::_connection()->connection->totsy->users;
				
		$id = new MongoID(Session::read('_id'));
		
		$counter = User::find('first', array('conditions' => array('_id' => $id), 'fields' => 'AddressCounter'))->data('AddressCounter');

		if ($counter >= User::$_maxAddress) {
			$message = 'There are already 10 addresses registered. Please remove one first.';
		} else {
			//Add address to mongo
			$success = $collection->update(array('_id' => $id ), array('$push' => array('Addresses' => $data)));
				
			//Increment address counter
			$success = $collection->update(array('_id' => $id ), array('$inc' => array('AddressCounter' => 1)));
			
			$message = 'Thank you for adding your address';			
		}		
		return compact('success', 'message');

	}
	
	
	
	
	
}


?>