<?php

namespace app\controllers;
use app\models\Address;
use \lithium\storage\Session;
use app\models\Navigation;
use \MongoId;




class AddressesController extends \lithium\action\Controller {
	/**
	 * The maximum number of addresses a user can have stored
	 * @var int
	 */
	private $_maxAddresses = 10; 
	
	public function view(){

		$this->_render['layout'] = 'main';

		$addressList = Address::find('all', array('conditions' => array('user_id' => Session::read('_id'))))->data();
		
		return compact("addressList");
	}
	
	private function getUser($fields = array()) {

		$id = new MongoID(Session::read('_id'));
		return User::find('first', array('conditions' => array('_id' => $id), $fields))->data();	
	}
	
	/**
	 * Adds an address 
	 */
	public function add() {
		$status = '';
		$message = '';
		$this->_render['layout'] = 'main';
					
		if($this->request->data){
			$userId = array('user_id' => new MongoID(Session::read('_id')));
			unset($this->request->data['submit']);
			$data = array_merge($userId, $this->request->data);
			
			$count = Address::count(array('user_id' => Session::read('_id')));

			if($count >= $this->_maxAddresses) {
				$message = 'There are already 10 addresses registered. Please remove one first.';
			} else {
				$Address = Address::create($data);
				$status = $Address->save($data);
				$message = 'Address Saved';
			}
			
		}
		return compact('status', 'message');
	}
		
	/**
	 * Sets up the Navigation element for the page
	 */
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