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
	}
	
	public function view() {
		if ($user = Session::read('userLogin')) {
			$addresses = Address::all(array(
				'conditions' => array('user_id' => (string) $user['_id'])
			));
		}
		return compact("addresses");
	}
	
	/**
	 * Adds an address
	 */
	public function add() {
		if ($this->request->is('ajax')) {
			$this->_render['layout'] = 'empty';
			$isAjax = true;
		} else {
			$isAjax = false;
		}
		$status = '';
		$message = '';
		$action = 'add';
		$address = Address::create($this->request->data);
		$user = Session::read('userLogin');

		if ($this->request->data) {
			$count = Address::count(array('user_id' => (string) $user['_id']));

			if ($count >= $this->_maxAddresses) {
				$message = "There are already {$this->_maxAddresses} addresses registered. ";
				$message .= "Please remove one first.";
			} else {

				if (($this->request->data['default'] == '1') && (Address::changeDefault($user['_id']))) {
					$message = 'This address is now your default';
				} elseif ($address->validates()) {
					$message = 'Address Saved';
				}
				$address->default = ($this->request->data['default'] == '1') ? true : false;
				$address->user_id = (string) $user['_id'];

				if ($address->save()) {
					if (!empty($this->request->data['isAjax'])) {
						$this->redirect('/shopping/checkout');
					} else {
						$this->redirect('/addresses');
					}
				}
			}
		}
		return compact('status', 'message', 'address', 'action', 'isAjax');
	}
	
	public function edit($_id) {
		$isAjax = false;
		$message = '';
		$action = 'edit';
		//Use the add template and main layout
		$this->_render['template'] = 'add';
		$user = Session::read('userLogin');
		
		if (!empty($_id)){
			//Find address using user_id and address_id
			$address = Address::find('first', array(
				'conditions' => array(
					'_id' => $_id,
					'user_id' => (string) $user['_id']
			)));
			if(empty($address)) {
				$this->redirect('/addresses/add');
			}
		}
		if (($this->request->data) && $address->save($this->request->data)) {
				$message = 'Your address has been updated';
		}

		return compact('message', 'address', 'action', 'isAjax');
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