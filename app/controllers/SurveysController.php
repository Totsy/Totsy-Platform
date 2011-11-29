<?php

namespace app\controllers;

use app\models\Survey;

class SurveysController extends \lithium\action\Controller {

	public function index() {
		$surveys = Survey::all();
		return compact('surveys');
	}

	public function view() {
		$survey = Survey::first($this->request->id);
		return compact('survey');
	}

	public function add() {
		$survey = Survey::create();

		if (($this->request->data) && $survey->save($this->request->data)) {
			$this->redirect(array('Surveys::view', 'args' => array($survey->id)));
		}
		return compact('survey');
	}

	public function edit() {
		$survey = Survey::find($this->request->id);

		if (!$survey) {
			$this->redirect('Surveys::index');
		}
		if (($this->request->data) && $survey->save($this->request->data)) {
			$this->redirect(array('Surveys::view', 'args' => array($survey->id)));
		}
		return compact('survey');
	}
}

?>