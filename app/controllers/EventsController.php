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
		if (!empty($event)) {
			foreach ($event->items as $value) {
				$item = Item::first(array('conditions' => array('_id' => $value, 'enabled' => "1")));
				if (!empty($item)) {
					$items[] = $item;
				}
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