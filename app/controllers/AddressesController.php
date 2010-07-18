<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use app\models\Menu;
use app\models\User;
use \lithium\storage\Session;

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
			$menu = Menu::find('all', array(
				'conditions' => array(
					'location' => 'left', 
					'active' => 'true'
			)));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
	}
	
	public function view() {
		$user = Session::read('userLogin');
		if (!empty($user)) {
			$addresses = Address::find('all', array(
				'conditions' => array(
					'user_id' => $user['_id']
			)));
		}
		return compact("addresses");
	}
	
	/**
	 * Adds an address
	 */
	public function add() {
		$status = '';
		$message = '';
		$address = Address::create();
		$user = Session::read('userLogin');
		if ($this->request->data) {
			$count = Address::count(array('user_id' => $user['_id'] ));
			if($count >= $this->_maxAddresses) {
				$message = 'There are already 10 addresses registered. Please remove one first.';
			} else {
				$this->request->data['default'] = ($this->request->data['default'] == '1') ? true : false;
				$data = array_merge($this->request->data, array('user_id' => ((string) $user['_id'])));
				$status = $address->save($data);
				$message = 'Address Saved';
			}
			
		}

		return compact('status', 'message', 'address');
	}
	
	public function edit($_id) {
		$message = '';

		//Use the add template and main layout
		$this->_render['template'] = 'add';
		$user = Session::read('userLogin');
		
		if (!empty($_id)){
			//Find address using user_id and address_id
			$address = Address::find('first', array(
				'conditions' => array(
					'_id' => $_id,
					'user_id' => $user['_id']
			)));
			if(empty($address)) {
				$this->redirect('/addresses/add');
			}
		}
		if (($this->request->data) && $address->save($this->request->data)) {
				$message = 'Address Updated';
		}

		return compact('message', 'address');
	}
	
	public function remove() {

		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				Address::remove(array('_id' => "$key"));
			}
		}
		$this->render(array('layout' => false));
		
		return true;
	}
}
?>