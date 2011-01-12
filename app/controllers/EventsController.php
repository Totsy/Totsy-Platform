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

		$itemCounts = $this->inventoryCheck(Event::open(array(
			'fields' => array('items')
		)));

		return compact('openEvents', 'pendingEvents', 'itemCounts');
	}

	public function view() {
		$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = $this->request->event;

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
		
		if ($event->end_date->sec < time()) {
			$this->redirect('/');
		}
		$pending = ($event->start_date->sec > time() ? true : false);
		
		if ($pending == false) {
			++$event->views;
			$event->save();
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

	public function inventoryCheck($events) {
		$events = $events->data();
		foreach ($events as $eventItems) {
			$count = 0;
			$id = $eventItems['_id'] ;

			if (isset($eventItems['items'])) {
				foreach ($eventItems['items'] as $eventItem) {
					if ($item = Item::first(array('conditions' => array('_id' => $eventItem)))) {
						if ($item->total_quantity) {
							$count += $item->total_quantity;
						}
					}
				}
			}
			$itemCounts[$id] = $count;
		}
		return $itemCounts;
	}

}



?>