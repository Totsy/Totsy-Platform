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
		
		$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
			return array('event' => null, 'items' => array(), 'shareurl');
		}
		
		$pending = ($event->start_date->sec > time() ? true : false);
		
		if ($pending == false) {
			if (!empty($event->items)) {
				foreach ($event->items as $_id) {
					$conditions = compact('_id') + array('enabled' => true);

					if ($item = Item::first(compact('conditions'))) {
						$items[] = $item;
					}
				}
			}
			$type = 'Today\'s';
		} else {
			$items = null;
			$type = 'Coming Soon';
		}


		return compact('event', 'items', 'shareurl', 'type');
	
	}
}



?>