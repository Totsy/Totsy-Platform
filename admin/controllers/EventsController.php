<?php

namespace admin\controllers;

use admin\controllers\BaseController;
use admin\models\Event;
use admin\models\User;
use admin\models\Order;
use admin\models\Item;
use lithium\storage\Session;
use MongoCode;
use MongoDate;
use MongoRegex;
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
	 * Limit characters for event\deal short description
	 */
	private $shortDescLimit = 90;

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

	public function combineskus($id = null) {
	    $this->_render['layout'] = false;

		//books event id hardcoded
		$_id = (string)"4ee6437f943e83b010000007";

		//items and orders collection calls
		$itemsCollection = Item::Collection();
		$ordersCollection = Order::collection();

		//blank array for items
		$items = array();

		//query events table for items
		$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));

		foreach ($eventItems as $item) {
			//add item ids to items array
			$items[] = (string) $item['_id'];
		}

		//mongo query to get orders with these items
		$orders = $ordersCollection->find(array('items' => array('$elemMatch' => array('item_id' => array('$in' => $items)))));

		foreach ($orders as $order) {
			//total items in order
			$orderitemCount = count($order['items']);

			//loop through items in order
			for($i=0; $i<$orderitemCount; $i++){
				//check it size is NULL
				if($order['items'][$i]['size']=="NULL"){
					//set size to 'no size'
					$order['items'][$i]['size'] = "no size";

					//save revised order
					$ordersCollection->save($order);

				}
			}
		}
		exit();

	}


	public function view($id = null) {
		$event = Event::find($id);
		if (empty($event)) {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}

		return compact('event');
	}

	//public function uploadcheck_clearance() {
	public function uploadcheck() {
	    $this->_render['layout'] = false;
	    unset($branch);
		//$this->_render['head'] = true;
		$fullarray = Event::convert_spreadsheet($this->request->data['ItemsSubmit']);
		return Event::check_spreadsheet($fullarray, $this->_mapCategories);
	}


	public function regeneratesku($_id = null) {
	    $this->_render['layout'] = false;
	    $items = Item::collections('items');
		//query items by eventid
		$eventItems = $items->find( array('event' => $_id))->sort(array('created_date' => 1));
		//return Item::generateskusbyevent($_id, true);
		//query items by eventid
		return Item::generateSku($eventItems);

	}

	public function generatesku($_id = null) {
	    $this->_render['layout'] = false;
		$this->_render['template'] = 'regeneratesku';
		return Item::generateskusbyevent($_id);
	}


	protected function parseItems_clearance($fullarray, $_id, $enabled = false) {
	    $this->_render['layout'] = false;

		$items_quantities = array();
		$items_ages = array();
		$items_categories = array();
		$items_prices = array();
		$items_skus = array();
		$items_skus_used = array();
		$items = array();

		$itemsCollection = Item::Collection();

		//convert textarea content into an array
		//$fullarray = Event::convert_spreadsheet($_POST['ItemsSubmit']);

		//loop thru form-created array to create an skus array, and a quantity array with the skus as keys
		foreach($fullarray as $item_sku_quantity){
			//$current_sku = trim($item_sku_quantity[0]);
			//$items_skus[] = $current_sku;
			//$items_quantities[$current_sku] = trim($item_sku_quantity[1]);
			//$items_prices[$current_sku] = trim($item_sku_quantity[2]);
		}


		$highestRow = $fullarray[0];
		$totalrows = count($fullarray);
		$totalcols = count($highestRow);


		$check_decimals = array("msrp", "sale_retail", "percentage_off", "percent_off", "orig_wholesale", "orig_whol", "sale_whol", "sale_wholesale", "imu");

		for ($row = 0; $row <= $totalrows; ++ $row ) {
			if($row>0&&$fullarray[$row][0]){
				$current_sku = $fullarray[$row][0];
				if($current_sku){
					$items_skus[] = $current_sku;
				}
			}
			for ($col = 0; $col < $totalcols; ++ $col) {
				$val = $fullarray[$row][$col];

				if ($row == 0) {
					if(($val)||($val==0)){
						$heading[] = $val;
					}
				} else {
					if (isset($heading[$col])) {
						if($heading[$col] === "quantity") {
							if (!empty($val)) {
								$items_quantities[$current_sku] = trim($val);
							}
						} else if($heading[$col] === "sale_retail") {
							if (!empty($val)) {
								$items_prices[$current_sku] = trim($val);
							}
						} else if(strstr($heading[$col], "age_")) {
							if (!empty($val)&&strlen($val)>1) {
								$items_ages[$current_sku][] = trim($val);
							}
						} else if(strstr($heading[$col], "category_")) {
							if (!empty($val)&&strlen($val)>1) {
								$items_categories[$current_sku][] = trim($val);
							}
						}
					}
				}
			}
		}


		//mongo query, find all items with skus
		$items_with_skus = Item::find('all', array('conditions' => array( 'skus' => array( '$in' => $items_skus))));

		//loop through returned item results
		foreach($items_with_skus as $olditem){

			//boolean to skip insert
			$addnewitem = true;

			//set new total quantity at 0
			$total_quantity_new = 0;

			//item data
			$oitem = $olditem;
			# 01/03/2011 - it was done this way because lithium had a bug with the data() function
			# So until that bug is fix, we will do it this way
			$oitem = get_object_vars($olditem);
			$oitem = $oitem['_config']['data'];

			//existing sku and sku_details
			$sku_details_arr = $oitem['sku_details'];
			$skus_arr = $oitem['skus'];
			$details_arr = $oitem['details'];
			$sale_details_arr = $oitem['sale_details'];

			//set quantities to 0
			foreach($details_arr as $details_key => $details){
				$oitem['details'][$details_key] = 0;
			}

			//loop thru sku_details, find the one we want, get the position in index
			foreach($sku_details_arr as $sku_details_key => $sku_details){

				//checks if current sku_details sku is in form-submitted SKU array
				if(in_array($sku_details, $items_skus)){
					if(in_array($sku_details, $items_skus_used)){
						$addnewitem = false;
					}
					else{
						$items_skus_used[] = $sku_details;

						//this is a match, get the index of the sku_details
						//echo "<br> * this is the index " . $sku_details_key;


						//current quantity (should be 0)
						$quantitynow = $details_arr[$sku_details_key];

						//echo "<br> * update quantity to " . $items_quantities[$sku_details];

						//use index to update quantity
						$oitem['details'][$sku_details_key] = (int)$items_quantities[$sku_details];

						$oitem['ages'] = $items_ages[$sku_details];
						$oitem['categories'] = $items_categories[$sku_details];



						//set sales to 0 for all sizes
						//$oitem['sale_details'][$sku_details_key]['sale_count'] = 0;

						//use index to get new price
						$item_price_new = $items_prices[$sku_details];

						$total_quantity_new += $items_quantities[$sku_details];

						//remove this sku from items_skus
						//$key = array_search($sku_details, $items_skus);
						//unset($items_skus[$key]);
					}
				}
			}

			if($addnewitem){
				//remove _id
				unset($oitem['_id']);
				unset($oitem['event']);
				unset($oitem['created_date']);
				unset($oitem['total_quantity']);
				unset($oitem['enabled']);
				unset($oitem['details_original']);
				unset($oitem['sale_details']);

				//update event _id
				$oitem['event'] = array((string)$_id);

				//update date
				$oitem['created_date'] = new MongoDate();

				//update enabled
				$oitem['enabled'] = (bool)$enabled;

				//create a new item instance
				$newItem = Item::create();

				//set total quant
				$oitem['total_quantity'] = (int)$total_quantity_new;

				//set new price
				if($item_price_new){
					unset($oitem['sale_retail']);
					$oitem['sale_retail'] = floatval($item_price_new);
				}

				//save original quants
				$oitem['details_original'] = $oitem['details'];

				//hack for xmas items
				if($this->request->data['miss_christmas']){
					$oitem['miss_christmas'] = true;
				}


				//save original quants
				//$oitem['sale_details'] = $oitem['sale_details'];

				//save item with revised info
				$newItem->save($oitem);

				//get _id of new item
				$new_id = $newItem->_id;

				//add new _id to returned items array
				$items[] = $new_id;
			}
		}
		return $items;
	}


	public function inventory($_id = null) {
	    $this->_render['layout'] = false;

		$event = Event::find($_id);

		$eventItems = array();

		$alleventids = array($_id);

		foreach($alleventids as $thiseventid){
			$eventItems = Item::find('all', array('conditions' => array('event' => $alleventids),
					'order' => array('created_date' => 'ASC')
				));
		}
		return compact('eventItems','event');
	}




	public function add() {

		$shortDescLimit = $this->shortDescLimit;

		if (empty($event)) {
			$event = Event::create();
		}

		if (!empty($this->request->data)) {
		    $images = $this->parseImages();
		    $seconds = ':'.rand(10,60);
		    $this->request->data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
		    $this->request->data['end_date'] = new MongoDate(strtotime($this->request->data['end_date'].$seconds));
		    if (isset($this->request->data['short_description']) && strlen($this->request->data['short_description'])>$shortDescLimit){
		    	$this->request->data['short_description'] = substr($this->request->data['short_description'],0,$shortDescLimit);
		    } else if (empty($this->request->data['short_description'])) {
		    	$this->request->data['short_description'] = $this->description_cutter($this->request->data['short_description'],$shortDescLimit);
		    }
		    $url = $this->cleanUrl($this->request->data['name']);
		    $eventData = array_merge(
		    	Event::castData($this->request->data),
		    	compact('items'),
		    	compact('images'),
		    	array('created_date' => new MongoDate()),
		    	array('url' => $url)
		    );
		    $changed = "<strong>Created " . $this->request->data['name'] . " Event</strong><br/>";
		    $modification_datas["author"] = User::createdby();
		    $modification_datas["date"] = new MongoDate(strtotime('now'));
		    $modification_datas["type"] = "modification";
		    $modification_datas["changed"] = $changed;

		    //Pushing modification datas to db
		    $modifications = $event->modifications;
		    $modifications[] = $modification_datas;
		    $eventData[modifications] = $modifications;
		    //Remove this when $_schema is setup
		    unset($eventData['itemTable_length']);
		    if ($event->save($eventData)) {
		    	$this->redirect(array('Events::edit', 'args' => array($event->_id)));
		    }
		}

		return compact('event','shortDescLimit');
	}

	public function edit($_id = null) {
		$shortDescLimit = $this->shortDescLimit;
		$current_user = Session::read('userLogin');

		$itemsCollection = Item::Collection();
		$event = Event::find($_id);
		$seconds = ':'.rand(10,60);
		$eventItems = Item::find('all', array('conditions' => array('event' => array($_id)),
				'order' => array('created_date' => 'ASC')
			));

		//process new items
		if(!empty($this->request->data['ItemsSubmit'])) {
			$enableItems = $this->request->data['enable_items'];

			$fullarray = Event::convert_spreadsheet($this->request->data['ItemsSubmit']);
			if($event->clearance){
				$parseItems = $this->parseItems_clearance($fullarray, $event->_id, $enableItems);
			}
			else{
				$parseItems = $this->parseItems($fullarray, $event->_id, $enableItems);
			}

			if (is_array($parseItems)){

				$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));
				if (!empty($eventItems)) {
					foreach ($eventItems as $item) {
						$items[] = (string) $item->_id;
					}
				}
			}
		}


		#T Get all possibles value for the multiple departments select
		$result = Item::getDepartments();
		$all_filters = array();
		foreach ($result['values'] as $value) {
			if($value&&$value!=" "){
				$all_filters[$value] = $value;
			}
			if (array_key_exists('Momsdads',$all_filters) && !empty($all_filters['Momsdads'])) {
				$all_filters['Momsdads'] = 'Moms & Dads';
			}
		}

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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//			if ($_FILES['upload_file']['error'] == 0 && $_FILES['upload_file']['size'] > 0) {
//				if (is_array($this->parseItems($_FILES, $event->_id, $enableItems))) {
//					unset($this->request->data['upload_file']);
//					$eventItems = Item::find('all', array('conditions' => array('event' => array($_id))));
//					if (!empty($eventItems)) {
//						foreach ($eventItems as $item) {
//							$items[] = (string) $item->_id;
//						}
//					}
//				}
//			}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




			$images = $this->parseImages($event->images);

			//Saving the original start and end and ship dates for comparison
			$start_date = $this->request->data['start_date'];
			$end_date = $this->request->data['end_date'];
			$ship_date = $this->request->data['ship_date'];

			$this->request->data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
			$this->request->data['end_date'] = new MongoDate(strtotime($this->request->data['end_date'].$seconds));
			if (isset($this->request->data['short_description']) && strlen($this->request->data['short_description'])>$shortDescLimit){
				$this->request->data['short_description'] = substr($this->request->data['short_description'],0,$shortDescLimit);
			} else if (empty($this->request->data['short_description'])){
				$this->request->data['short_description'] = $this->description_cutter($this->request->data['short_description'],$shortDescLimit);
			}
			$url = $this->cleanUrl($this->request->data['name']);
			$eventData = array_merge(
				Event::castData($this->request->data),
				compact('items', 'images', 'departments'),
				array('url' => $url)
			);

			// Comparison of OLD Event attributes and the NEW Event attributes
			$changed = "";

			if ($eventData[name] != $event->name) {
				$changed .= "Name changed from <strong>{$event->name}</strong> to <strong>{$eventData[name]}</strong><br/>";
			}

			if ($eventData[blurb] != $event->blurb) {
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

			if ($eventData[ship_message] != $event->ship_message) {
				$changed .= "Ship Message changed from <strong>{$event->ship_message}</strong> to <strong>{$eventData[ship_message]}</strong><br/>";
			}

			if (strtotime($ship_date) != $event->ship_date->sec) {
				$temp =  date('m/d/Y H:i:s', $event->ship_date->sec);
				$changed .= "Ship Date changed from  <strong>{$temp}</strong> to <strong>{$ship_date}</strong><br/>";
			}

			if ($eventData[enable_items] != $event->enable_items) {
				$changed .= "Enabled Items from <strong>{$event->enable_items}</strong> to <strong>{$eventData[enable_items]}</strong><br/>";
			}

			/**
			* Changed author save from email to user id because email can change even if it is a Totsy Email
			**/
			$modification_datas["author"] = $current_user['_id'];
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
		/**
		* Retrieving firstname and lastname/emails of modifiers
		**/
		if ($event->modifications) {
			foreach($event->modifications as $log){
				$user = User::find('first', array(
					'conditions' => array('_id' => $log['author'])
					));
				if($user){
					if ($user->firstname) {
						$log['author'] = $user->firstname . " " . $user->lastname;
					} else {
						$log['author'] = $user->email;
					}
				}
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

		return compact('event', 'eventItems', 'items', 'all_filters', 'shortDescLimit');
	}

	/**
	 * Locate an existing event by some criteria. Currently supported
	 * criteria (via querystring parameters) include:
	 *  'name'
	 *
	 * @return void
	 */
	public function find() {
		$collEvents = Event::collection();
		$events     = $collEvents->find(array(
			'name' => $this->request->query['name']
		));

		$results = array();
		foreach($events as $evt) {
			$results[] = $evt;
		}

		echo json_encode(array('total' => count($results), 'results' => $results));
		$this->_render['head'] = true;
	}

	/**
	 * This method parses the item file that is uploaded in the Events Edit View.
	 *
	 * @todo Move this method to the Items controller and make it a static method.
	 * @todo Add event to the header information for spreadsheet (event - this needs to replace vendor)
	 * @todo Add vendor_description
	 */
	protected function parseItems($array, $_id, $enabled = false) {
		$eventItems = array();
		$items = array();
		$itemIds = array();
		$relatedItems = array();
		$rowToItemIdMap = "";

		// Default column headers from csv file
		$standardHeader = array(
			'vendor',
			'vendor_style',
			'age',
			'ages',
			'departments',
			'categories',
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
			'shipping_dimensions',
			'related_items'
		);

		$highestRow = $array[0];
		$totalrows = count($array);
		$totalcols = count($highestRow);

		$check_decimals = array("msrp", "sale_retail", "percentage_off", "percent_off", "orig_wholesale", "orig_whol", "sale_whol", "sale_wholesale", "imu");

		for ($row = 0; $row <= $totalrows; ++ $row ) {
			for ($col = 0; $col < $totalcols; ++ $col) {
				$val = $array[$row][$col];

				if ($row == 0) {
					if(($val)||($val==0)){
						$heading[] = $val;
					}
				} else {
					if (isset($heading[$col])) {
						if ((in_array($heading[$col], $check_decimals))&&(!empty($val))) {
							$val = floatval($val);
						}
						if(($heading[$col] === "department_1") || ($heading[$col] === "department_2") || ($heading[$col] === "department_3") || (strstr($heading[$col], "department_1")) || (strstr($heading[$col], "department_2")) || (strstr($heading[$col], "department_3"))) {
							if (!empty($val)&&strlen($val)>1) {
								$eventItems[$row - 1]['departments'][] = ucfirst(strtolower(trim($val)));
								$eventItems[$row - 1]['departments'] = array_unique($eventItems[$row - 1]['departments']);
							}
						} else if(strstr($heading[$col], "age_")) {
							if (!empty($val)&&strlen($val)>1) {
								$eventItems[$row - 1]['age'] = trim($val);
								$eventItems[$row - 1]['ages'][] = trim($val);
								$eventItems[$row - 1]['ages'] = array_unique($eventItems[$row - 1]['ages']);
							}
						} else if(strstr($heading[$col], "category_")) {
							if (!empty($val)&&strlen($val)>1) {
								$eventItems[$row - 1]['category'] = trim($val);
								$eventItems[$row - 1]['categories'][] = trim($val);
								$eventItems[$row - 1]['categories'] = array_unique($eventItems[$row - 1]['categories']);
							}
						} else if (($heading[$col] === "related_1") || ($heading[$col] === "related_2") || ($heading[$col] === "related_3") || ($heading[$col] === "related_4") || ($heading[$col] === "related_5")) {
							if (!empty($val)) {
								$eventItems[$row - 1]['related_items'][] = trim($val);
								$eventItems[$row - 1]['related_items'] = array_unique($eventItems[$row - 1]['related_items']);
							}
						} else {
							if (!empty($val)) {
								$eventItems[$row - 1][$heading[$col]] = $val;
							}
						}
					}
				}
			}
		}

		foreach ($eventItems as $itemDetail) {
			$i=0;
			$itemAttributes = array_diff_key($itemDetail, array_flip($standardHeader));

  			//check radio box for 'final sale' text append
  			$enableFinalsale = $this->request->data['enable_finalsale'];

  			//check radio box for 'final sale' text append
  			$miss_christmas = $this->request->data['miss_christmas'];

  			//check if final sale radio box was checked or not
  			if($enableFinalsale==1){
  			  $blurb = "<p><strong>Final Sale</strong></p>";
  			}
  			//if not make blurb var blank for good form
  			else{
  			  $blurb = "";
  			}
			$itemCleanAttributes = null;
			foreach ($itemAttributes as $key => $value) {
				unset($itemDetail[$key]);

				if($key!=="color_description_style") {
					$itemCleanAttributes[trim($key)] = (string)$value;
				}
			}
			$item = Item::create();
			$date = new MongoDate();
			$url = $this->cleanUrl($itemDetail['description']." ".$itemDetail['color']);

			$details = array(
				'enabled' => (bool) $enabled,
				'miss_christmas' => (bool) $miss_christmas,
				'created_date' => $date,
				'details' => $itemCleanAttributes,
				'details_original' => $itemCleanAttributes,
				'event' => array((string) $_id),
				'url' => $url,
				'blurb' => $blurb,
				'taxable' => true
			);

			$newItem = array_merge(Item::castData($itemDetail), Item::castData($details));
			$newItem['vendor_style'] = (string) $newItem['vendor_style'];

			if ((array_sum($newItem['details']) > 0) && $item->save($newItem)) {
				$items[] = (string) $item->_id;

				//related items will be added later, after ihe items in this event actually HAVE unique ID's
				//each related item will momentarily be a string made of the color, description and style separated by pipes
				if( !empty($itemDetail['related_items']) ) {

					$k=0;

					foreach( $itemDetail['related_items'] as $key=>$value ) {
						//build array of related items using color, description and style
						//the color and the description are for the buyer to see, but we use the style number
						//here to persist the related items
						//and later update each item using it and the event hash to query and get the id
						$fields = explode("|", $value);

						$related_items[(string) $item->_id][$k]['vendor_style'] = $fields[2];
						$related_items[(string) $item->_id][$k]['event'] = (string) $_id;

						$k++;
					}
				}
			}
			$i++;
		}

		$itemsCollection = Item::Collection();

		foreach ( $related_items as $key => $value ) {

			$rel_items = array();

			//aggregate related item id's
			for ($i=0; $i<count($related_items[$key]); $i++) {

				$style = trim($related_items[$key][$i]['vendor_style']);
				$event = trim($related_items[$key][$i]['event']);

				//query for this item
				$rel_item = Item::find('first', array('conditions' => array(
							'event' => array($event),
							'vendor_style' => $style
						)));

				$rel_items[] = (string) $rel_item['_id'];

			}

			$itemsCollection->update(array("_id" => new MongoId($key)), array('$set' => array('related_items' => $rel_items)));
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

	private function description_cutter($str,$length=null){
		$return = '';
		$str = strip_tags($str);
		$split = preg_split("/[\s]+/",$str);
		$len = 0;
		if (is_array($split) && count($split)>0){
			foreach($split as $splited){
				$tmp_len = $len + strlen($splited) +1;
				if ($tmp_len < $length){
					$len = $tmp_len;
					$return.= $splited.' ';
				} else {
					break;
				}
			}
		}

		if (strlen($return)>0){
			return $return;
		} else {
			return $str;
		}
	}
}

?>