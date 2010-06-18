<?php

namespace app\controllers;
use app\models\User;
use app\models\Address;
use app\models\Navigation;
use \lithium\storage\Session;


/**
 * Handles the users main account information.
 */
class AccountController extends \lithium\action\Controller {
	
	/**
	 * @var array Contains the address information of the user
	 */
	private $addresses; 
	
	/**
	 * Get the main address information and set for the view
	 * @return array
	 */
	public function index(){
	
		$this->_render['layout'] = 'main';
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
		$this->_render['layout'] = 'main';
		
		if ($this->request->data) {		
			$User = $this->getUser();
			//Update database using $set			
			$success = $User->save($this->request->data);
			if($success) {
				//Update the session with correct names	
				Session::write('firstname', $this->request->data['firstname']);
				Session::write('lastname', $this->request->data['lastname']);
			}
		}
		
		$data = $this->getUser();
		
		return compact("data", "success");
		
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

		$id = Session::read('_id');
		return User::find('first', array('conditions' => array('_id' => $id), $fields));	
	}
			
	public function news() {
		$data = "";
		$this->_render['layout'] = 'main';
		
		$user = $this->getUser();
		if ($this->request->data) {
			foreach ($this->request->data as $key=>$value){
				$data[] = $key;
			}
			$data = array('Newsletter' => $data);
			$user = $this->getUser();
			//Remove Submit Button
			unset($this->request->data['submit']);
			$success = $user->save($data);
		}
		
		return compact("data");
	}
	
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$navigation = Navigation::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$id = Session::read('_id');
			$userRecord = User::find('first', array('conditions' => array('_id' => $id)));
			if ($userRecord == null) {
				//@todo: This default configuration should be set somewhere else
				$userInfo = array('firstname' => '', 'lastname' => 'Guest', 'email' => '');
			} else {
				$userInfo = $userRecord->data();
			}
			$self->set(compact('navigation', 'userInfo'));
			return $chain->next($self, $params, $chain);
		});
	}
}

?>