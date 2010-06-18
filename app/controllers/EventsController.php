<?php

namespace app\controllers;

use \app\models\Event;
use \app\models\Item;

class EventsController extends \lithium\action\Controller {

	public function index() {
		$events = Event::all();
		return compact('events');
	}

	public function view($id = null) {
		$event = Event::find($id);
		return compact('event');
	}

	public function add() {

		$items = Item::all();
		if (!empty($this->request->data)) {
			$event = Event::create($this->request->data);
			if ($event->save()) {
				$this->redirect(array(
					'controller' => 'events', 'action' => 'view',
					'args' => array($event->id)
				));
			}
		}
		if (empty($event)) {
			$event = Event::create();
		}
		return compact('event', 'items');
	}

	public function edit($id = null) {
		$event = Event::find($id);
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($event->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'events', 'action' => 'view',
					'args' => array($event->id)
				));
			}
		}
		return compact('event');
	}
}

?>