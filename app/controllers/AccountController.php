<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use \lithium\storage\Session;

/**
 * Handles the users main account information.
 */
class AccountController extends BaseController {
	
	public function index(){

		$user = Session::read('userLogin');

		$billing = Address::find('first', array(
			'conditions' => array(
				'user_id' => $user['_id'],
				'type' => "Billing",
				'default' => true
		)));
		$shipping = Address::find('first', array(
			'conditions' => array(
				'user_id' => $user['_id'],
				'type' => "Shipping",
				'default' => true
		)));

		return compact('billing', 'shipping');
	}
}

?>