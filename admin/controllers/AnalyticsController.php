<?php

namespace admin\controllers;

use \admin\models\Analytic;

class AnalyticsController extends \lithium\action\Controller {

	public function index() {
		$analytics = Analytic::all();
		return compact('analytics');
	}

	public function view() {
		$analytic = Analytic::first($this->request->id);
		return compact('analytic');
	}

	public function add() {
		$analytic = Analytic::create();

		if (($this->request->data) && $analytic->save($this->request->data)) {
			$this->redirect(array('Analytics::view', 'args' => array($analytic->id)));
		}
		return compact('analytic');
	}

	public function edit() {
		$analytic = Analytic::find($this->request->id);

		if (!$analytic) {
			$this->redirect('Analytics::index');
		}
		if (($this->request->data) && $analytic->save($this->request->data)) {
			$this->redirect(array('Analytics::view', 'args' => array($analytic->id)));
		}
		return compact('analytic');
	}
}

?>