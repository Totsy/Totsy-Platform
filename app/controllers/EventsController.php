<?php

namespace app\controllers;

use \app\models\Event;
use \MongoDate;

class EventsController extends \lithium\action\Controller {

	public function index() {
		$now = new MongoDate(time());
		$events = Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => $now)
		)));
		$this->_render['layout'] = 'main';
		return compact('events');
	}

	public function view() {
		$event = Event::first($this->request->id);
		return compact('event');
	}

	public function add() {
		$event = Event::create();

		if (($this->request->data) && $event->save($this->request->data)) {
			$this->redirect(array('Events::view', 'args' => array($event->id)));
		}
		return compact('event');
	}

	public function edit() {
		$event = Event::find($this->request->id);

		if (!$event) {
			$this->redirect('Events::index');
		}
		if (($this->request->data) && $event->save($this->request->data)) {
			$this->redirect(array('Events::view', 'args' => array($event->id)));
		}
		return compact('event');
	}
}



?>