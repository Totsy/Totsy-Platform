<?php

namespace app\controllers;

use \app\controllers\BaseController;
use \app\models\Event;
use \app\models\Item;
use \MongoDate;
use \lithium\storage\Session;

class EventsController extends BaseController {

	public function index() {
		$now = new MongoDate(time());
		$tomorrow = new MongoDate(time() + (24 * 60 * 60));
		$twoWeeks = new MongoDate(time() + (7 * 24 * 60 * 60));
		$eventsToday = Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => $now,
					'$lt' => $tomorrow)),
			'order' => array('end_date' => 'ASC')
		));
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
				'start_date' => array(
					'$gt' => $twoWeeks
		))));
		$this->_render['layout'] = 'main';
		return compact('eventsToday', 'currentEvents', 'futureEvents');
	}

	public function view($url) {
		$this->_render['layout'] = 'main';
		if ($url == 'comingsoon') {
			$this->_render['template'] = 'soon';
		}
		$event = Event::first(array('conditions' => array('enabled' => '1', 'url' => $url)));
		if (!empty($event)) {
			foreach ($event->items as $value) {
				$items[] = Item::first(array('conditions' => array('_id' => $value)));
			}
		} else {
			$this->_render['template'] = 'noevent';
		}
		return compact('event', 'items');
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