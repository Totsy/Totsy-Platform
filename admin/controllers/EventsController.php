<?php

namespace admin\controllers;

use \admin\models\Event;
use \admin\models\Item;
use \MongoDate;
use \MongoID;

class EventsController extends \lithium\action\Controller {

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
	
	public function index() {
		$events = Event::all();
		return compact('events');
	}

	public function view($id = null) {
		$event = Event::find($id);
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}

		return compact('event');
	}

	public function add() {
		
		$created_date = 0;
		$modified_date = 0;
		$files = 0; 
		if (empty($event)) {
			$event = Event::create();
		}
		/*
			TODO Clean up file handling here
		*/
		if ($_FILES) {
			$items = $this->parseItems($_FILES);
			unset($this->request->data['upload_file']);
		}
		if (!empty($this->request->data)) {
			$images = $this->praseImages();
			$startDate = strtotime($this->request->data['start_date']);
			$endDate = strtotime($this->request->data['end_date']); 
			$this->request->data['start_date'] = new MongoDate($startDate);
			$this->request->data['end_date'] = new MongoDate($endDate);
			$eventData = array_merge($this->request->data, compact('items'), compact('images'));
			if ($event->save($eventData)) {	
				$this->redirect(array('Events::view', 'args' => array($event->_id)));
			}
		}

		return compact('event');
	}

	public function edit($_id = null) {
		$event = Event::find($_id);
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}
		if ($_FILES) {
			$items = $this->parseItems($_FILES);
			unset($this->request->data['upload_file']);
		}
		if (!empty($this->request->data)) {
			$images = $this->praseImages();
			$startDate = strtotime($this->request->data['start_date']);
			$endDate = strtotime($this->request->data['end_date']); 
			$this->request->data['start_date'] = new MongoDate($startDate);
			$this->request->data['end_date'] = new MongoDate($endDate);
			$eventData = array_merge($this->request->data, compact('items'), compact('images'));
			
			if ($event->save($eventData)) {
				$this->redirect(array(
					'controller' => 'events', 'action' => 'edit',
					'args' => array($event->_id)
				));
			}
		}
		return compact('event');
	}
	
	protected function parseItems($_FILES) {
		$items = array();
		$itemIds = array();
		// Default column headers from csv file
		$standardHeader = array(
			'vendor',
			'vendor_style',
			'age',
			'category',
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
					$c = count($data);
					// Capture the key row
					for ($y = 0; $y < $c ; $y++) { 
						$key[] = $data[$y];
					}
					// Remove the first heading
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
				// Add some more information to array
				$details = array('active' => 1, 'created_date' => $date, 'details' => $itemAttributes );
				$newItem = array_merge($itemDetail, $details);
				if ($item->save($newItem)) {
					$items[] = $item->_id;
				}
			}
		}
		return $items;
	}
	
	protected function praseImages() {
		$images = array();
		$uploadFileIds = array_diff_key($this->request->data, array_flip($this->eventKey));
		if (!empty($uploadFileIds)) {
			foreach ($uploadFileIds as $key => $value) {
				$images[$key] = $value;
				unset($this->request->data[$key]);
			}
		}
		return $images;
	}
}

?>