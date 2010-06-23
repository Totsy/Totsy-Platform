<?php

namespace app\controllers;

use \app\models\Credit;

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

		if (($this->request->data) && $credit->save($this->request->data)) {
			$this->redirect(array('Credits::view', 'args' => array($credit->id)));
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