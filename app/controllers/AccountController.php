<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\models\Address;
use app\models\Menu;
use \lithium\storage\Session;


/**
 * Handles the users main account information.
 */
class AccountController extends BaseController {
	
	/**
	 * @var array Contains the address information of the user
	 */
	private $addresses; 
	
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$userInfo = Session::read('userLogin');
			$self->set(compact('menu', 'userInfo'));
			return $chain->next($self, $params, $chain);
		});
	}
	/**
	 * Get the main address information and set for the view
	 * @return array
	 */
	public function index(){
	
		$success = $this->setAddressInfo();
		if($success){
			$addresses = $this->addresses;
		}	
		$routing = array('url' => '/addresses/add', 'message' => 'Add Address');
		return compact('addresses', 'routing');
	}
	
	/**
	 * Updates the user information in both the db and session
	 * @return array
	 */
	public function info() {
		$success = false;

		
		if ($this->request->data) {		
			$User = $this->getUser();
			//Update database using $set			
			$success = $User->save($this->request->data);
			if($success) {
				//Update the session with correct names	
				$info = Session::read('userLogin');
				$info['firstname'] = $this->request->data['firstname'];
				$info['lastname'] = $this->request->data['lastname'];
				Session::write('userLogin', $info);
				$this->redirect('/account/info');
			}
		}
		
		$user = $this->getUser();
		
		return compact("user", "success");
		
	}
	
	/**
	 * Set the main address information to the Account object $this
	 *
	 * The database is queried for the default billing and shipping information for the user
	 * If no address is found then false is returned
	 * @var array
	 * @return bool
	 */
	private function setAddressInfo($types = array('Billing', 'Shipping')) {
		$success = false;
		
		foreach ($types as $value){
			$address = Address::find('first', array(
					'conditions' => array(
						'user_id' => Session::read('_id'), 
						'type' => "$value", 
						'default' => 'Yes'
						)
					));
			if($address){				
				$this->addresses["$value"] = $address->data();
				$url = 'addresses/edit/'. $address->data('_id');
				$urlArray = array('url' => $url);						
				$this->addresses["$value"] = array_merge($this->addresses["$value"], $urlArray);	
			}
			$success = true;
		}
		return $success;
	}
	
	private function getUser($fields = array()) {

		$user = Session::read('userLogin');
		return User::find('first', array('conditions' => array('_id' => $user['_id']), $fields));	
	}
	

}

?>