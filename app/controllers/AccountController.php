<?php

namespace app\controllers;
use app\models\User;
use app\models\Address;
use app\models\Navigation;
use \lithium\storage\Session;
use \MongoId;




class AccountController extends \lithium\action\Controller {
	
	
	private $addresses; 
	

	
	public function index(){
		$userInfo = $this->getUser();
		$this->_render['layout'] = 'main';

		$success = $this->setAddressInfo();
		if($success){
			$addresses = $this->addresses;
		}	
		$routing = array('url' => '/addresses/add', 'message' => 'Add Address');
		return compact('userInfo', 'addresses', 'routing');
	}
	
	
	public function info() {
		$success = false;
		if ($this->request->data) {			
			//Update database using $set			
			$success = User::update($this->request->data);
			
			//Update the session with correct names	
			Session::write('firstname', $this->request->data['firstname']);
			Session::write('lastname', $this->request->data['lastname']);			
		}

		$data = $this->getUser();
		
		return compact("data", "success");
		
	}
	
	private function setAddressInfo($types = array('Billing', 'Shipping')) {
		$success = false;
		
		foreach ($types as $value){
			$address = Address::find('first', array('conditions' => array(
					'user_id' => Session::read('_id'), 
					'type' => "$value", 
					'default' => 'Yes')));
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

		$id = new MongoID(Session::read('_id'));
		return User::find('first', array('conditions' => array('_id' => $id), $fields))->data();	
	}
			
	public function news() {
		$data = "";
		return compact("data");
	}
	
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$navigation = Navigation::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$self->set(compact('navigation'));
			return $chain->next($self, $params, $chain);
		});
	}
		
}
?>