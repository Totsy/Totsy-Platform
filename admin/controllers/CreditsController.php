<?php

namespace admin\controllers;

use \admin\models\Credit;
use \admin\models\User;
use lithium\util\Validator;

class CreditsController extends \lithium\action\Controller {

	public function index() {
		$credits = Credit::all();
		return compact('credits');
	}

	public function view() {
		$credit = Credit::first($this->request->id);
		return compact('credit');
	}

	public function add() {
		$credit = Credit::create();
		$isMoney = Validator::isMoney($this->request->data['amount']);
		if (($isMoney) && ($this->request->data) && Credit::add($credit, $this->request->data)) {
			if (User::applyCredit($this->request->data)) {
				$this->redirect(array('Users::view', 'args' => array($this->request->data['user_id'])));
			}
		} else {
			$this->redirect(array('Users::view', 'args' => array(
				$this->request->data['user_id']
			)));
		}
		return compact('credit');
	}

	public function edit() {
		$credit = Credit::find($this->request->id);

		if (!$credit) {
			$this->redirect('Credits::index');
		}
		if (($this->request->data) && $credit->save($this->request->data)) {
			$this->redirect(array('Credits::view', 'args' => array($credit->id)));
		}
		return compact('credit');
	}
}

?>