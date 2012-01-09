<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use lithium\storage\Session;

/**
 * Handles the users main account information.
 */
class AccountController extends BaseController {
	
	public function index () {

		$user = Session::read('userLogin');

		$billing = Address::find('first', array(
				'conditions' => array(
					'$or' => array(
						array('user_id' => (string) $user['_id'],
							  'type' => "Billing"),
						array('user_id' => (string) $user['_id'],
							  'default' =>  "1",
							  'type' => "Billing")))));				  
							  
		$shipping = Address::find('first', array(
		    'conditions' => array(
		    	'$or' => array(
		    		array('user_id' => (string) $user['_id'],
		    			  'type' => "Shipping"),
		    		array('user_id' => (string) $user['_id'],
		    			  'default' =>  "1",
		    			  'type' => "Shipping"))))); 
									  						
		if($this->request->is('mobile')){
			$this->_render['layout'] = 'mobile_main';
			$this->_render['template'] = 'mobile_index';
		}
		return compact('billing', 'shipping');
	}
}

?>