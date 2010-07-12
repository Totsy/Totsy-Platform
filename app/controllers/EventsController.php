<?php

namespace app\controllers;

use \app\models\Event;
use \MongoDate;

class EventsController extends \lithium\action\Controller {

	public function index() {
		$now = new MongoDate(time());
		$tomorrow = new MongoDate(time() + (24 * 60 * 60));
		$twoWeeks = new MongoDate(time() + (7 * 24 * 60 * 60));
		$eventsToday = Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => $now,
					'$lt' => $tomorrow
		))));
		$currentEvents = Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => $tomorrow,
					'$lt' => $twoWeeks
		))));
		$futureEvents = Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => $twoWeeks
		))));
		$this->_render['layout'] = 'main';
		return compact('eventsToday', 'currentEvents', 'futureEvents');
	}

	public function view($name) {
		
		$event = Event::first(array('conditions' => array('enabled' => '1', 'name' => $name)));
		die(var_dump($event->data()));
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