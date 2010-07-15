<?php

namespace app\controllers;

use \app\controllers\BaseController;
use \app\models\Event;
use \app\models\Item;
use \MongoDate;
use \lithium\storage\Session;

class EventsController extends BaseController {
	
	public function index() {
		$openEvents = Event::open();
		$pendingEvents = Event::pending();

		return compact('openEvents', 'pendingEvents');
	}

	public function view($url) {
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
}



?>