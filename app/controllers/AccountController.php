<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use \lithium\storage\Session;

/**
 * Handles the users main account information.
 */
class AccountController extends BaseController {
	
	public function index() {

		$user = Session::read('userLogin');

		$billing = Address::find('first', array(
				'conditions' => array(
					'$or' => array(
						array('user_id' => (string) $user['_id']),
						array('user_id' => (string) $user['_id'],
							  ''  	
							)
			))));

		
		$shipping = Address::find('first', array(
				'conditions' => array(
					'$or' => array(
						array('user_id' => (string) $user['_id']),
						array('user_id' => (string) $user['_id'],
								)
			))));
						
		return compact('billing', 'shipping');
	}
}

?>