<?php

namespace app\controllers;

use \app\models\Event;
use \app\models\Item;
use \MongoDate;

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
		$created_date = 0;
		$modified_date = 0;
		$files = 0; 
		$items = Item::find('all', array(
			'fields' => compact(
				'created_date', 
				'modified_date', 
				'files'
		)));
		/*
			TODO Clean up file handling here
		*/
		if ($_FILES) {
			$items = $this->parseItems($_FILES);
			unset($this->request->data['upload_file']);
		}
		if (!empty($this->request->data)) {
			$startDate = strtotime($this->request->data['start_date']);
			$endDate = strtotime($this->request->data['end_date']); 
			$this->request->data['start_date'] = new MongoDate($startDate);
			$this->request->data['end_date'] = new MongoDate($endDate);
			$eventData = array_merge($this->request->data, $items);
			$event = Event::create($eventData);
			if ($event->save()) {
				$this->redirect(array(
					'controller' => 'events', 'action' => 'index',
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
	
	protected function parseItems($_FILES) {
		$itemIds = array();
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
				$item = Item::create();
				$date = new MongoDate();
				// Add some more information to array
				$details = array('active' => 1, 'created_date' => $date);
				$newItem = array_merge($itemDetail, $details);
				if ($item->save($newItem)) {
					$items[] = $item->_id;
				}
			}
		}
		return compact('items');
	}
}

?>