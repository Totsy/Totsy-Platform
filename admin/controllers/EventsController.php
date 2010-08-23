<?php

namespace admin\controllers;

use admin\controllers\BaseController;
use admin\models\Event;
use admin\models\Item;
use \MongoDate;
use \MongoId;

/**
 * Administrative functionality to create and edit events. 
 */
class EventsController extends BaseController {

	/**
	 * List of event keys that should be in the view
	 * @var array List of accepted event keys
	 */
	private $eventKey = array(
		'name',
		'description',
		'blurb',
		'start_date',
		'end_date',
		'enabled'
	);
	/**
	 * Grab all the events from mongo
	 */
	public function index() {
		$events = Event::all();
		return compact('events');
	}
	/**
	 * 
	 */
	public function view($id = null) {
		$event = Event::find($id);
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}

		return compact('event');
	}

	public function add() {
	
		if (empty($event)) {
			$event = Event::create();
		}
		if ($_FILES) {
			$items = $this->parseItems($_FILES, $event->_id);
			unset($this->request->data['upload_file']);
		}
		if (!empty($this->request->data)) {
			$images = $this->parseImages();
			$seconds = ':'.rand(10,60);
			$this->request->data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
			$this->request->data['end_date'] = new MongoDate(strtotime($this->request->data['end_date'].$seconds));
			$url = $this->cleanUrl($this->request->data['name']);
			$eventData = array_merge(
				Event::castData($this->request->data),
				compact('items'), 
				compact('images'), 
				array('created_date' => new MongoDate()),
				array('url' => $url)
			);
			//Remove this when $_schema is setup
			unset($eventData['itemTable_length']);
			if ($event->save($eventData)) {	
				$this->redirect(array('Events::edit', 'args' => array($event->_id)));
			}
		}

		return compact('event');
	}

	public function edit($_id = null) {
		$event = Event::find($_id);
		$seconds = ':'.rand(10,60);
		$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));

		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'add'));
		}
		if (!empty($this->request->data)) {
			unset($this->request->data['itemTable_length']);
			if ($_FILES['upload_file']['error'] == 0) {
				//THIS IS A HACK!!
				$success = $this->parseItems($_FILES, $event->_id);
				unset($this->request->data['upload_file']);
				$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));
				if (!empty($eventItems)) {
					foreach ($eventItems as $item) {
						$items[] = (string) $item->_id;
					}
				}
			}
			$images = $this->parseImages($event->images);
			$this->request->data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
			$this->request->data['end_date'] = new MongoDate(strtotime($this->request->data['end_date'].$seconds));
			$url = $this->cleanUrl($this->request->data['name']);
			$eventData = array_merge(
				Event::castData($this->request->data),
				compact('items'),
				compact('images'),
				array('url' => $url)
			);
			if ($event->save($eventData)) {
				
				$this->redirect(array(
					'controller' => 'events', 'action' => 'edit',
					'args' => array($event->_id)
				));
			}
		}
		if ($event->items) {
			foreach ($event->items as $_id) {
				$conditions = compact('_id') + array('enabled' => true);

				if ($item = Item::first(compact('conditions'))) {
					$items[] = $item;
				}
			}
		}

		return compact('event', 'eventItems', 'items');
	}
	/**
	 * Parse the CSV file
	 */
	protected function parseItems($_FILES, $_id) {
		$items = array();
		$itemIds = array();
		// Default column headers from csv file
		$standardHeader = array(
			'vendor',
			'vendor_style',
			'age',
			'category',
			'sub_category',
			'description',
			'color',
			'total_quantity',
			'msrp',
			'sale_retail',
			'percent_off',
			'orig_whol',
			'sale_whol',
			'imu',
			'product_weight',
			'product_dimensions',
			'shipping_weight',
			'shipping_dimensions'
		);
		if ($_FILES['upload_file']['type'] == 'text/csv') {
			// Open the File.
			if (($handle = fopen($_FILES['upload_file']['tmp_name'], "r")) !== FALSE) {
				// Set the parent multidimensional array key to 0.
				$nn = 0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					
					// Count the total keys in the row.
					$c = count($data) - 1;
					// Capture the key row
					for ($y = 0; $y < $c ; $y++) {
						if (strlen($data[$y]) > 0) {
							$key[] = $data[$y];
						}
					}
					// Populate the multidimensional array.
					for ($x = 0; $x < $c ; $x++) {
						$eventItems[$nn][$key[$x]] = $data[$x];
					}
					$nn++;
				}
				// Close the File.
				fclose($handle);
			}

			// Remove the heading array
			unset($eventItems[0]);

			// Add items to db and get _ids
			foreach ($eventItems as $itemDetail) {
				unset($itemDetail['NULL']);
				// Group all the attributes together	
				$itemAttributes = array_diff_key($itemDetail, array_flip($standardHeader));
				// Unset all the attributes from main array so add back the details proper
				foreach ($itemAttributes as $key => $value) {
					unset($itemDetail[$key]);
				}
				$item = Item::create();
				$date = new MongoDate();
				$dirtyUrl = $itemDetail['description']." ".$itemDetail['color'];
				$url = $this->cleanUrl($dirtyUrl);
				
				// Add some more information to array
				$details = array(
					'enabled' => true, 
					'created_date' => $date, 
					'details' => $itemAttributes, 
					'event' => array($_id),
					'url' => $url
				);
				$newItem = array_merge(Item::castData($itemDetail), Item::castData($details));

				if ($item->save($newItem)) {
					$items[] = (string) $item->_id;
				}
			}
		}
		
		return $items;
	}
	/**
	 * Parse the images from the request using the key
	 * @param object
	 * @return array
	 */
	protected function parseImages($imageRecord = null) {
		$images = array();
		foreach ($this->request->data as $key => $value) {
			if (substr($key, -6) == '_image' ) {
				$images["$key"] = $value;
			}
		}
		if (empty($images) && !empty($imageRecord)) {
			$images = $imageRecord->data();
		}
		return $images;
	}

	public function preview($_id = null) {

		$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$event = Event::first(array(
			'conditions' => array(
				'_id' => $_id
		)));

		$pending = ($event->start_date->sec > time() ? true : false);

		if (!empty($event->items)) {
			foreach ($event->items as $_id) {
				$conditions = compact('_id') + array('enabled' => true);

				if ($item = Item::first(compact('conditions'))) {
					$items[] = $item;
				}
			}
		}
		if ($pending == false) {
			$type = 'Today\'s';
		} else {
			$type = 'Coming Soon';
		}
		$this->_render['layout'] = 'preview';
		$id = $event->_id;
		$preview = "Events";
		return compact('event', 'items', 'shareurl', 'type', 'id', 'preview');

	}
}

?>