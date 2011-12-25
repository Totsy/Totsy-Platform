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

	public function index() {
		$message = $this->request->query['message'];
	
		if ($user = Session::read('userLogin')) {
			$creditcards = CreditCard::retrieve_all_cards($user['_id']);
		}
		return compact("creditcards","message");
	}

	public function add_test() {
		$creditcard = $this->request->data;
		$user = Session::read('userLogin');

		$creditcard['number'] = "4111111111111111";
		$creditcard['year'] = "2015";
		$creditcard['month'] = "02";
		$creditcard['code'] = "222";
		
		$creditcard['firstname'] = 'deepen';
		$creditcard['lastname'] = 'shah';
		$creditcard['address'] = '185 asdf st';
		$creditcard['address2'] = '';
		$creditcard['city'] = 'New York';
		$creditcard['state'] = 'NY';
		$creditcard['zip'] = '10009';
												
		$vars['billingAddr']['firstname'] = $creditcard[firstname];
		$vars['billingAddr']['lastname'] = $creditcard[lastname];
		$vars['billingAddr']['address'] = $creditcard[address];
		$vars['billingAddr']['address2'] = $creditcard[address2];
		$vars['billingAddr']['city'] = $creditcard[city];
		$vars['billingAddr']['state'] = $creditcard[state];
		$vars['billingAddr']['zip'] = $creditcard[zip];
		$vars['user'] = $user;
		$vars['creditCard'] = $creditcard;

		$result = CreditCard::add($vars);
		
		if ($result == "success") {
			$message = "Your credit card was saved.";
		} else if ($result == "error") {
			$message = "There was an error saving this credit card.  Please try again.";
		} else if ($result == "duplicate") {
			$message = "You already have this credit card on file.";
		}
		
		print $message;
		
		return compact('message', 'creditcard', 'action');
	}

	public function add() {
		$status = '';
		$message = '';
		$action = 'add';
		$creditcard = $this->request->data;
		$user = Session::read('userLogin');

		if ($this->request->data) {		
			$vars['billingAddr']['firstname'] = $creditcard[firstname];
			$vars['billingAddr']['lastname'] = $creditcard[lastname];
			$vars['billingAddr']['address'] = $creditcard[address];
			$vars['billingAddr']['address2'] = $creditcard[address2];
			$vars['billingAddr']['city'] = $creditcard[city];
			$vars['billingAddr']['state'] = $creditcard[state];
			$vars['billingAddr']['zip'] = $creditcard[zip];
			$vars['user'] = $user;
			$vars['creditCard'] = $creditcard;

			$result = CreditCard::add($vars);
			
			if ($result == "success") {
				$message = "Your credit card was saved.";
			} else if ($result == "error") {
				$message = "There was an error saving this credit card.  Please try again.";
			} else if ($result == "duplicate") {
				$message = "You already have this credit card on file.";
			}

			$this->redirect('/creditcards/?message='.urlencode($message));
		}
		
		return compact('status', 'message', 'creditcard', 'action');
	}


	public function remove() {
		$user = Session::read('userLogin');
		$profileID = $this->request->query['profileID'];
		
		if ($this->request->query) {
			foreach ($this->request->query as $key => $value) {
				CreditCard::remove($user['_id'], $key);
			}
		}

		$this->render(array('layout' => false));
		
		return true;
	}
}
?>