<?php

namespace app\controllers;

use \app\controllers\BaseController;
use \app\models\Event;
use \app\models\Item;
use app\models\Banner;
use \MongoDate;
use \lithium\storage\Session;
use app\models\Affiliate;


class EventsController extends BaseController {

	public function index() {
		$bannersCollection = Banner::collection();
		$banner = $bannersCollection->findOne(array("enabled" => true, 'end_date' => array('$gte' => new MongoDate(strtotime('now')))));
		$openEvents = Event::open();
		$pendingEvents = Event::pending();

		$itemCounts = $this->inventoryCheck(Event::open(array(
			'fields' => array('items')
		)));
		//Sort events open/sold out
		foreach ($openEvents as $key => $event) {
			foreach ($itemCounts as $event_id => $quantity) {
				if ($quantity == 0 && ((string)$event["_id"] == $event_id)) {
					$events_closed[] = $openEvents[$key];
					unset($openEvents[$key]);
				}
			} 
		}
		if (!empty($events_closed)) {
			if (!empty($openEvents)) {
				foreach ($events_closed as $event) {
					$openEvents[] = $event;
				}
			} else {
				$openEvents = $events_closed;
			}
		}
		return compact('openEvents', 'pendingEvents', 'itemCounts', 'banner');
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
			$this->redirect('/sales ');
		}
		$pending = ($event->start_date->sec > time() ? true : false);

		if ($pending == false) {
			++$event->views;
			$event->save();
			if (!empty($event->items)) {
				foreach ($event->items as $_id) {
					$conditions = compact('_id') + array('enabled' => true);

					if ($item = Item::first(compact('conditions'))) {
						if ($item->total_quantity <= 0) {
							$items_closed[] = $item;
						} else {
							$items[] = $item;
						}
					}
				}
				//Sort items open/sold out
				if (!empty($items_closed)) {
					if(!empty($items)) {
						foreach ($items_closed as $item) {
							array_push($items, $item);
						}
					} else {
						$items = $items_closed;
					}
				}
			}
			$type = 'Today\'s';
		} else {
			$items = null;
			$type = 'Coming Soon';
		}

		$pixel = Affiliate::getPixels('event', 'spinback');
		$spinback_fb = Affiliate::generatePixel('spinback', $pixel,
			                                            array('event' => $_SERVER['REQUEST_URI'])
			                                            );
		return compact('event', 'items', 'shareurl', 'type', 'spinback_fb');

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
	public function disney(){
	    $this->_render['layout'] = false;
	}

}



?>