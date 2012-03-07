<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;
use app\models\Item;
use app\models\Banner;
use MongoDate;
use lithium\storage\Session;
use app\models\Affiliate;


class EventsController extends BaseController {
// if you change this here also remember to change it in mobile and the app (it's hard coded)
	public function category() {
		$datas = $this->request->args;
		$categories = array();
		
	
		if(empty($this->request->args[0])) {
//			$openEvents = Event::open()->data();
			$this->redirect('/sales');
		} else {
			$map = Event::mapCat($this->request->params['action']); 
			$categories =  $map[ $this->request->args[0] ];
			if($categories==""){
				$this->redirect('/'.$this->request->args[0]);
			}
			$openEvents = Event::open(null,array(),null,$categories)->data();
			unset($map);
		}

		$itemCounts = array();
		$eventCount = count($openEvents);
		$itemsCollection = Item::collection();

		for($i=0; $i<$eventCount; $i++){
			$eventId = (string)$openEvents[$i]['_id'];
			
			//$items = $itemsCollection->find( array('event' =>  array($eventId)) )
			$items = $itemsCollection->find(array(
												'event' =>  array($eventId), 
												'categories' => array('$in' => array($categories)), 
												'enabled' => true,
												'total_quantity' => array('$gt'=>0)
												))
									  ->limit(6);		
			
			// when event dosn't have enabled and not soled out items ..
			// remove the events from a list
			if ($items->count()==0){
				unset($openEvents[$i]);
				continue;
			}
			
			foreach($items as $eachitem){
				$openEvents[$i]['eventItems'][] = $eachitem;
			}
		}
		//check if mobile or not
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_category';
		}

		// re-count events num
		$eventCount = count($openEvents);
		
		// re-count events num
		$eventCount = count($openEvents);
		
		return compact('openEvents', 'items', 'categories', 'eventCount');
	}


	public function age() {
		$datas = $this->request->args;
		$categories = array();

		if(empty($this->request->args[0])) {
//			$openEvents = Event::open()->data();
			$this->redirect('/sales');
		} else {
			$map = Event::mapCat($this->request->params['action']); 
			$ages =  $map[ $this->request->args[0] ];
			if($ages==""){
				$this->redirect('/'.$this->request->args[0]);
			}
			$openEvents = Event::open(null,array(),null,null, $ages)->data();
			unset($map);
		}

		$itemCounts = array();
		$eventCount = count($openEvents);
		$itemsCollection = Item::collection();

		for($i=0; $i<$eventCount; $i++){
			$eventId = (string)$openEvents[$i]['_id'];

			//$items = $itemsCollection->find(array('event' =>  array($eventId)))

			$items = $itemsCollection->find(array(
												'event' =>  array($eventId), 
												'ages' => array('$in' => array($ages)), 
												'enabled' => true,
												'total_quantity' => array('$gt'=>0)
												))
									  ->limit(6);		
			
			// when event dosn't have enabled and not soled out items ..
			// remove the events from a list
			if ($items->count()==0){
				unset($openEvents[$i]);
				continue;
			}
			
			foreach($items as $eachitem){
				$openEvents[$i]['eventItems'][] = $eachitem;
			}
		}
		
		// re-count events num
		$eventCount = count($openEvents);
		
		//hack to make the ages appear on top of same view file		
		$categories = $ages;
		$this->_render['template'] = 'category';
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_age';
		}
		return compact('openEvents', 'items', 'categories', 'eventCount');
	}

	public function index() {
		
		$datas = $this->request->data;
		$departments = array();
		$bannersCollection = Banner::collection();
		$banner = $bannersCollection->findOne(array("enabled" => true, 'end_date' => array('$gte' => new MongoDate(strtotime('now')))));
		if(empty($this->request->args)) {
			$openEvents = Event::open();
			$pendingEvents = Event::pending();
		} else {
			$departments = ucwords($this->request->args[0]);
			$openEvents = Event::open(null,array(),$departments);
			$pendingEvents = Event::pending(null,array(),$departments);
		}

		$itemCounts = array();
		/*
		// DON'T COUNT ITEMS !!!!
		// IMPORTANT
		// Slav
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
		*/
		if (isset($events_closed) && !empty($events_closed)) {
			if (!empty($openEvents)) {
				foreach ($events_closed as $event) {
					$openEvents[] = $event;
				}
			} else {
				$openEvents = $events_closed;
			}
		}
		
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_index';
		}
	
		return compact('openEvents', 'pendingEvents', 'itemCounts', 'banner', 'departments');	
	}

	public function view() {
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_view';
		} else {
		
		}
		$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = $this->request->event;

		$departments = '';
		if(!empty($this->request->query['filter'])) {
			$departments = ucwords($this->request->query['filter']);
		}
		if($this->request->data){
			$datas = $this->request->data;
			$departments = $datas["filterby"];
		}
		if(empty($departments)) {
			$departments = 'All';
		}
		if ($url == 'comingsoon') {
			$this->_render['template'] = 'soon';
		}
		$event = Event::first(array(
			'conditions' => array(
				'enabled' => true,
				'url' => $url
		)));
		if (!$event) {
			$event = Event::first(array(
				'conditions' => array(
				'viewlive' => true,
				'url' => $url
			)));
		}
		if (!$event) {
			
			$this->redirect('/search/'.$this->request->params['event'], array('exit' => true));
			$this->_render['template'] = '';
			return array('event' => null, 'items' => array(), 'shareurl');
		}

		if ($event->end_date->sec < time()) {
			$this->redirect('/sales ');
		}
		$pending = ($event->start_date->sec > time() ? true : false);

		if ($pending == false) {
			++$event->views;
			$event->save();
			if(!empty($departments)) {
				$filters = array('All' => 'All', ucwords($departments) => ucwords($departments));
			} else {
				$filters = array('All' => 'All');
			}
			if (!empty($event->items)) {
					$eventItems = Item::find('all', array( 'conditions' => array(
													'event' => array((string)$event->_id),
													'enabled' => true
												),
												'order' => array('created_date' => 'ASC')
											));
					foreach ($eventItems as $eventItem) {
						$result = $eventItem->data();

						if (array_key_exists('departments',$result) && !empty($result['departments'])) {
							if(in_array($departments,$result['departments']) ) {
								if ($eventItem->total_quantity <= 0) {
									$items_closed[] = $eventItem;
								} else {
									$items[] = $eventItem;
								}
							}
							foreach($eventItem->departments as $value) {
								$filters[$value] = $value;
							}
						}
					if ($departments == 'All') {
						if ($eventItem->total_quantity <= 0) {
							$items_closed[] = $eventItem;
						} else {
							$items[] = $eventItem;
						}
						if(!empty($eventItem->departments)) {
								foreach($eventItem->departments as $value) {
									$filters[$value] = $value;
								}
						}
					}
				}
				if (!empty($filters) && !empty($departments)) {
					$filters = array_unique($filters);
					if (array_key_exists('Momsdads',$filters) && !empty($filters['Momsdads'])) {
						$filters['Momsdads'] = 'Moms & Dads';
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

		return compact('event', 'items', 'shareurl', 'type', 'spinback_fb', 'departments', 'filters');
	}

	public function inventoryCheck($events) {
		$events = $events->data();
		$itemCounts = array();

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
