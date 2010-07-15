<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use \lithium\storage\Session;
use app\models\Menu;
use app\models\User;
use \MongoId;
use \lithium\data\Connections;
use \lithium\analysis\Logger;




class AddressesController extends BaseController {
	
	/**
	 * The maximum number of addresses a user can have stored
	 * @var int
	 */
	private $_maxAddresses = 10; 
	
	/**
	 * Sets up the Menu element for the page
	 */
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
	}
	
	public function view(){

		$this->_render['layout'] = 'main';
		$addresses = Address::find('all', array('conditions' => array('user_id' => Session::read('_id'))))->data();
		return compact("addresses");
	}
	
	private function getUser($fields = array()) {

		$id = new MongoID(Session::read('_id'));
		return User::find('first', array('conditions' => array('_id' => $id), $fields))->data();	
	}
	
	/**
	 * Adds an address
	 */
	public function add() {

		//Set some defaults
		$status = '';
		$message = '';
		$this->_render['layout'] = 'main';
		$address = Address::create();
		$user = Session::read('userLogin');		
		if($this->request->data){
			$count = Address::count(array('user_id' => $user['_id'] ));
			if($count >= $this->_maxAddresses) {
				$message = 'There are already 10 addresses registered. Please remove one first.';
			} else {
				$status = $address->save($this->request->data);
				$message = 'Address Saved';
			}
			
		}
		
		return compact('status', 'message', 'address');
	}
	
	public function edit($_id) {
		$message = '';

		//Use the add template and main layout
		$this->_render['template'] = 'add';

		if(!empty($_id)){
			//Find address using user_id and address_id
			$address = Address::find('first', array('conditions' => array('_id' => $_id)));
			if(empty($address)) {
				$this->redirect('/addresses/add');
			}
		}
		//Check if we got form data from $POST
		if($this->request->data) {
			$status = $address->save($this->request->data);
			//TODO: Remove the old information from the record
			if(!empty($status)) {
				$message = 'Address Updated';
			} else {
				//If this doesn't get updated we have database issues. 
				$message = 'Error';
			}			
		}

		return compact('message', 'address');
	}
}
?>