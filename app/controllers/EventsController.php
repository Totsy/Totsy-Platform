<?php

namespace app\controllers;

use \app\controllers\BaseController;
use \app\models\Event;
use \app\models\Item;
use \MongoDate;
use \lithium\storage\Session;

class EventsController extends BaseController {

	public function index() {
		$eventsToday = Event::today();
		$currentEvents = Event::current();
		$futureEvents = Event::future();

		$this->_render['layout'] = 'main';
		return compact('eventsToday', 'currentEvents', 'futureEvents');
	}

	public function view($url) {
		$this->_render['layout'] = 'main';

		if ($url == 'comingsoon') {
			$this->_render['template'] = 'soon';
		}
		$event = Event::first(array('conditions' => array('enabled' => '1', 'url' => $url)));

		if (!$event) {
			$this->_render['template'] = 'noevent';
			return array('event' => null, 'items' => array());
		}

		foreach ($event->items as $_id) {
			$conditions = compact('_id') + array('enabled' => "1");

			if ($item = Item::first(compact('conditions'))) {
				$items[] = $item;
			}
		}
		return compact('event', 'items');
	}

	public function add() {
		$event = Event::create();

		if (($this->request->data) && $event->save($this->request->data)) {
			$this->redirect(array('Events::view', 'id' => $event->_id));
		}
		return compact('event');
	}

	public function edit() {
		$event = Event::find($this->request->id);

		if (!$event) {
			$this->redirect('Events::index');
		}
		if (($this->request->data) && $event->save($this->request->data)) {
			$this->redirect(array('Events::view', 'id' => $event->_id));
		}
		return compact('event');
	}
}



?>