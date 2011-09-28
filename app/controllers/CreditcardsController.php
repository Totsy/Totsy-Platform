<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Address;
use app\models\CreditCard;
use app\models\Menu;
use app\models\User;
use \lithium\storage\Session;

class CreditcardsController extends BaseController {
	
	/**
	 * The maximum number of credit cards a user can have stored
	 * @var int
	 */
	private $_maxCreditCards = 10; 
	
	protected function _init() {
		parent::_init();
	}
	
	public function view() {
		if ($user = Session::read('userLogin')) {
			$creditcards = CreditCard::all(array(
				'conditions' => array('user_id' => (string) $user['_id'])
			));
		}
		return compact("creditcards");
	}

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
		$creditcard = CreditCard::create($this->request->data);
		$user = Session::read('userLogin');

		if ($this->request->data) {

			$count = CreditCard::count(array('user_id' => (string) $user['_id']));

			if ($count >= $this->_maxCreditCards) {
				$message = "There are already {$this->_maxCreditCards} credit cards registered. ";
				$message .= "Please remove one first.";
			} else {

				// if (($this->request->data['default'] == '1') && (Address::changeDefault($user['_id']))) {
				// 	$message = 'This address is now your default';
				// } elseif 
//				if ($address->validates()) {
//					$message = 'Address Saved';
//				}
				//$address->default = ($this->request->data['default'] == '1') ? true : false;
				$creditcard->user_id = (string) $user['_id'];

				if ($creditcard->save()) {
					if (!empty($this->request->data['isAjax'])) {
						$this->redirect('/shopping/checkout');
					} else {
						$this->redirect('Creditcards::view');
					}
				}
			}
		}
		return compact('status', 'message', 'creditcard', 'action', 'isAjax');
	}


	public function remove() {

		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				CreditCard::remove(array('_id' => "$key"));
			}
		}
		$this->render(array('layout' => false));
		
		return true;
	}
}
?>