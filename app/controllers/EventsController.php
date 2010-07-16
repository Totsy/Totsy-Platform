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
		$event = Event::first(array(
			'conditions' => array(
				'enabled' => true, 
				'url' => $url
		)));

		if (!$event) {
			$this->_render['template'] = 'noevent';
			return array('event' => null, 'items' => array());
		}

		if (!empty($event->items)) {
			foreach ($event->items as $_id) {
				$conditions = compact('_id') + array('enabled' => true);

				if ($item = Item::first(compact('conditions'))) {
					$items[] = $item;
				}
			}
		}
		
		$tweeturl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		return compact('event', 'items', 'tweeturl');
	
	}
}



?>