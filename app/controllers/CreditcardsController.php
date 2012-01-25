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
		$cyberSourceProfiles = array();
		
		if ($user = Session::read('userLogin')) {
			#Get CyberSourceProfiles recorded for this user
			$userInfos = User::lookup($user['_id']);
			$cyberSourceProfiles = array();
			if($userInfos['cyberSourceProfiles']) {
				$cyberSourceProfiles = $userInfos['cyberSourceProfiles'];
			}
		}
		return compact("cyberSourceProfiles","message");
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
			$vars['savedByUser'] = true;
			
			$result = CreditCard::add($vars);

		 	if ($result == "error") {
				$message = "There was an error saving this credit card.  Please try again.";
			} else if ($result == "duplicate") {
				$message = "You already have this credit card on file.";
			} else {
				$message = "Your credit card was saved.";
			}

			$this->redirect('/creditcards/?message='.urlencode($message));
		}
		
		return compact('status', 'message', 'creditcard', 'action');
	}


	public function remove() {
		$user = Session::read('userLogin');
		$profileID = $this->request->query['profileID'];
		
		CreditCard::remove_creditcard($user['_id'], $profileID);
		
		$message = "Your credit card was removed.";
		
		$this->redirect('/creditcards/?message='.urlencode($message));
	}
}
?>