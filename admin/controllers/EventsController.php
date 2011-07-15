<?php

namespace admin\controllers;

use admin\controllers\BaseController;
use admin\models\Event;
use admin\models\Item;
use lithium\storage\Session;
use MongoDate;
use MongoId;
use Mongo;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
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
		$current_user = Session::read('userLogin');

		$itemsCollection = Item::Collection();
		$event = Event::find($_id);
		$seconds = ':'.rand(10,60);
		$eventItems = Item::find('all', array('conditions' => array('event' => array($_id)),
												'order' => array('created_date' => 'ASC')
												)); 			
		#T Get all possibles value for the multiple departments select
		$result = Item::getDepartments();
		$all_filters = array();
		foreach ($result['values'] as $value) {
			$all_filters[$value] = $value;
			if (array_key_exists('Momsdads',$all_filters) && !empty($all_filters['Momsdads'])) {
				$all_filters['Momsdads'] = 'Moms & Dads';
			}
		}
		#END T
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'add'));
		}
		if (!empty($this->request->data)) {
			if(!empty($this->request->data['departments'])) {
				foreach($this->request->data['departments'] as $value) {
					if(!empty($value)) {
						$departments[] = ucfirst($value);
					}
				}
				foreach($eventItems as $item) {
					$itemsCollection->update(array('_id' => $item->_id), array('$set' => array("departments" => $departments)));
				}
				unset($this->request->data['departments']);
			}
			unset($this->request->data['itemTable_length']);
			$enableItems = $this->request->data['enable_items'];
			if ($_FILES['upload_file']['error'] == 0 && $_FILES['upload_file']['size'] > 0) {
				if (is_array($this->parseItems($_FILES, $event->_id, $enableItems))) {
					unset($this->request->data['upload_file']);
					$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));
					if (!empty($eventItems)) {
						foreach ($eventItems as $item) {
							$items[] = (string) $item->_id;
						}
					}
				}
			}
			$images = $this->parseImages($event->images);
			
			//Saving the original start and end and ship dates for comparison
			$start_date = $this->request->data['start_date'];
			$end_date = $this->request->data['end_date'];
			$ship_date = $this->request->data['ship_date'];
						
			$this->request->data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
			$this->request->data['end_date'] = new MongoDate(strtotime($this->request->data['end_date'].$seconds));
			$url = $this->cleanUrl($this->request->data['name']);
			$eventData = array_merge(
				Event::castData($this->request->data),
				compact('items'),
				compact('images'),
				array('url' => $url)
			);
			
			// Comparison of OLD Event attributes and the NEW Event attributes
			$changed = "";
			
			if (abs(strcmp($eventData[name], $event->name))  > 0) {
				$changed .= "Name changed from <strong>{$event->name}</strong> to <strong>{$eventData[name]}</strong><br/>";
			}
	
			if (abs(strcmp($eventData[blurb], $event->blurb))  > 0) {
				$changed .= "Blurb changed from <strong>{$event->blurb}</strong> to <strong>{$eventData[blurb]}</strong><br/>";
			}

			if ($eventData[enabled] != $event->enabled) {
				$changed .= "Enabled changed from <strong>{$event->enabled}</strong> to <strong>{$eventData[enabled]}</strong><br/>";
			}

			if (strtotime($start_date) != $event->start_date->sec) {
				$temp =  date('m/d/Y H:i:S', $event->start_date->sec);
				$changed .= "Start Date changed from <strong>{$temp}</strong> to <strong>{$start_date}</strong><br/>";
			}
			
			if (strtotime($end_date) != $event->end_date->sec) {
				$temp =  date('m/d/Y H:i:s', $event->end_date->sec);
				$changed .= "End Date changed from  <strong>{$temp}</strong> to <strong>{$end_date}</strong><br/>";
			}
			
			if (abs(strcmp($eventData[ship_message], $event->ship_message))  > 0) {
				$changed .= "Ship Message changed from <strong>{$event->ship_message}</strong> to <strong>{$eventData[ship_message]}</strong><br/>";
			}
			
			if (strtotime($ship_date) != $event->ship_date->sec) {
				$temp =  date('m/d/Y H:i:s', $event->ship_date->sec);
				$changed .= "Ship Date changed from  <strong>{$temp}</strong> to <strong>{$ship_date}</strong><br/>";
			}			
			
			if ($eventData[enable_items] != $event->enable_items) {
				$changed .= "Enabled Items from <strong>{$event->enable_items}</strong> to <strong>{$eventData[enable_items]}</strong><br/>";
			}

			$modification_datas["author"] = $current_user['email'];
			$modification_datas["date"] = new MongoDate(strtotime('now'));
			$modification_datas["type"] = "modification";
			$modification_datas["changed"] = $changed;

			//Pushing modification datas to db
			$modifications = $event->modifications;
			$modifications[] = $modification_datas;
			$eventData[modifications] = $modifications;
		
			// End of Comparison of OLD Event Attributes and NEW event attributes

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

		return compact('event', 'eventItems', 'items', 'all_filters');
	}
	/**
	 * This method parses the item file that is uploaded in the Events Edit View.
	 *
	 * @todo Move this method to the Items controller and make it a static method.
	 * @todo Add event to the header information for spreadsheet (event - this needs to replace vendor)
	 * @todo Add vendor_description
	 */
	protected function parseItems($_FILES, $_id, $enabled = false) {
		$items = array();
		$itemIds = array();
		// Default column headers from csv file
		$standardHeader = array(
			'vendor',
			'vendor_style',
			'age',
			'departments',
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
		if ($this->request->data) {
			if ($_FILES['upload_file']['error'] == 0) {
				$file = $_FILES['upload_file']['tmp_name'];
				$objReader = PHPExcel_IOFactory::createReaderForFile("$file");
				$objPHPExcel = $objReader->load("$file");
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($row = 1; $row <= $highestRow; ++ $row) {
						for ($col = 0; $col < $highestColumnIndex; ++ $col) {
							$cell = $worksheet->getCellByColumnAndRow($col, $row);
							$val = $cell->getValue();
							if ($row == 1) {
								$heading[] = $val;
							} else {
								if (!in_array($heading[$col], array('','NULL'))) {
										if(($heading[$col] == "department_1") ||
												($heading[$col] == "department_2") ||
													($heading[$col] == "department_3") ) {
											if (!empty($val)) {
												$eventItems[$row - 1]['departments'][] = trim($val);
												$eventItems[$row - 1]['departments'] = array_unique($eventItems[$row - 1]['departments']);
											}
										} else {
											$eventItems[$row - 1][$heading[$col]] = $val;
										}
									
								}
							}
 						}
 					}
				}
				foreach ($eventItems as $itemDetail) {
					$itemAttributes = array_diff_key($itemDetail, array_flip($standardHeader));
					foreach ($itemAttributes as $key => $value) {
						unset($itemDetail[$key]);
						$itemCleanAttributes[trim($key)] = $value;
					}
					$item = Item::create();
					$date = new MongoDate();
					$url = $this->cleanUrl($itemDetail['description']." ".$itemDetail['color']);
					$details = array(
						'enabled' => (bool) $enabled,
						'created_date' => $date,
						'details' => $itemCleanAttributes,
						'event' => array((string) $_id),
						'url' => $url,
						'taxable' => true
					);
					$newItem = array_merge(Item::castData($itemDetail), Item::castData($details));
					if ((array_sum($newItem['details']) > 0) && $item->save($newItem)) {
						$items[] = (string) $item->_id;
					}
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
			$eventItems = Item::find('all', array( 'conditions' => array(
													'event' => array($_id),
													'enabled' => true
												),
												'order' => array('created_date' => 'ASC')
			));										
			foreach($eventItems as $eventItem) {
				$items[] = $eventItem;
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