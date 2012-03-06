<?php

namespace admin\extensions\command;

use admin\models\ProcessedOrder;
use admin\models\Item;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\ItemMaster;
use admin\models\PurchaseOrder;
use admin\models\Queue;
use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;
use admin\extensions\command\Base;
use admin\extensions\command\Exchanger;
use admin\extensions\command\MakeSku;
use lithium\analysis\Logger;
use admin\extensions\util\String;
use admin\extensions\command\Pid;
use admin\extensions\Mailer;

/**
 * This command is the main processor to manage the transmission of files to our 3PL
 *
 * The export specification is based on the DotCom flat file integration.
 * Based on what has been queued from the admin system all the event ids will be processed
 * for order, item and/or PO transmission.
 *
 * @see http://projects.totsy.com/projects/tech/wiki/Dotcom
 * @see admin/extensions/command/Exchanger
 * @see admin/controllers/QueueController
 */



//sets maxlifetime to 5 hours
ini_set("session.gc_maxlifetime", "18000");

//check the maxlifetime
//echo ini_get("session.gc_maxlifetime");

//specifies session path to avoid default maxlifetime value of 24 mins to override
session_save_path('/www/admin/resources/totsy/tmp');

//to check the session path
//echo session_save_path();

ini_set('session.gc_probability', 1);

//check the gc_probability
//echo ini_get("session.gc_probability");


class OrderExport extends Base {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * The test boolean string flag will prevent saves to database. 'false' is the default.
	 *
	 * @var boolean
	 */
	public $test = 'false';

	/**
	 * The array list of events that should be batched
	 * processed.
	 *
	 * @var array
	 */
	protected $events = array();

	/**
	 * A summary of information that will be mailed to a group.
	 *
	 * @var array
	 */
	protected $summary = array();

	/**
	 * Any files that should be excluded during import.
	 *
	 * @var array
	 */
	protected $_exclude = array(
		'.',
		'..',
		'.DS_Store',
		'processed',
		'empty'
	);

	/**
	 * Allows verbose info logging. (default = false)
	 */
	public $verbose = 'false';

	/**
	 * Directory of tmp files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/tmp/';

	/**
	 * Directory of files holding the backup files to FTP.
	 *
	 * @var string
	 */
	public $processed = '/resources/totsy/processed/';

	/**
	 * Directory of files holding the backup files to FTP.
	 *
	 * @var string
	 */
	public $pending = '/resources/totsy/pending/';

	/**
	 * Full path to file.
	 */
	protected $path = null;

	/**
	 * Additional events associated with orders not in
	 * original queue.
	 * @var array
	 */
	protected $addEvents = array();

	/**
	 * Order Events for processing
	 * @var array
	 */
	protected $orderEvents = array();

	/**
	 * PO Events for processing
	 * @var array
	 */
	protected $poEvents = array();

	/**
	 * Current Queue in process
	 * @var array
	 */
	protected $queue = array();

	/**
	 * Main method for exporting Order and PO files.
	 *
	 * The run method will query the pending event transactions
	 * that have not yet been processed. This queuing system will be managed
	 * from the admin dashboard.
	 *
	 * @todo Remove the environment set to the base command.
	 * @todo Make sure a queue cannot contain two empty arrays.
	 */
	public function run() {
		MongoCursor::$timeout = -1;
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$this->processed = LITHIUM_APP_PATH . $this->processed;
		$this->pending = LITHIUM_APP_PATH . $this->pending;
		$this->log("...Waking up...");
		$pid = new Pid($this->tmp,  'OrderExport');
		$start = time();
		if ($pid->already_running == false) {
			$conditions = array('processed' => array('$ne' => true));
			$records = Queue::find('all', compact('conditions'));
			foreach ($records as $queue) {
				$this->summary = array();
				$this->log("Processing Queue Record: $queue->_id");
				if ($queue) {
					$this->batchId = array('order_batch' => $queue->_id);
					$this->log("Starting to process $queue->_id");
					$this->time = date('ymdHis');
					$queueData = $queue->data();
					$this->queue = $queue;
					if($this->queue->run_amount) {
						$this->queue->run_amount += 1;
					} else {
						$this->queue->run_amount = 1;
					}
					if ($queueData['orders']) {

						$this->orderEvents = $queueData['orders'];
						$this->_orderGenerator();
					}
					if ($queueData['purchase_orders']) {
					    $this->queue->status = "Processing PO File";
					    $this->queue->save();
						$this->poEvents = $queueData['purchase_orders'];
						$this->_purchases();
					}
					$this->queue->status = "Processing Item File";
					$this->queue->save();
					$this->_itemGenerator();
					$this->log("Finised processing: $queue->_id");
					if ($queueData['orders'] || $queueData['purchase_orders']) {
						$queue->summary = $this->summary;
						$queue->processed = true;
						$queue->processed_date = new MongoDate();
						$queue->save();
						$this->summary['from_email'] = 'no-reply@totsy.com';
						$this->summary['to_email'] = 'logistics@totsy.com';
						if ($this->test != 'true' && Environment::is('production')) {
                           Mailer::send('Order_Export', $this->summary['to_email'], $this->summary);
                        }
					}
				}
			}
		} else {
			$this->log("Already Running! Stoping Execution");
		}
		$end = time();
		$finish = $end - $start;
		$this->log("It took $finish secs");
	}

	/**
	 * The Order Generator
	 *
	 * Generates all the orders of the events that have been requested from the queue.
	 * All non-canceled orders are organized into the format specified by DotCom.
	 * They are saved to a staging folder which will be FTPed.
	 * @return boolean
	 */
	protected function _orderGenerator() {
	    MongoCursor::$timeout = -1;
		$this->log("Starting to process Orders");
		$orderCollection = Order::collection();
		$itemCollection = Item::connection()->connection->items;
		$orderFile = array();
		$heading = ProcessedOrder::$_fileHeading;
		/**
		* Check if this orders have been processed by this queue
		* If orders have been processed already, skip those orders
		**/
		$conditions = $this->batchId;
		$processCheck = ProcessedOrder::count(compact('conditions'));
		if ($processCheck > 0 ) {
		    $fields = array('');
		    $results = ProcessedOrder::find(compact('conditions'));
		}
		$orders = $orderCollection->find(array(
			'items.event_id' => array('$in' => $this->orderEvents),
			'cancel' => array('$ne' => true),
			'error_date' => array('$exists' => false)
		));
		
		$this->log('Calling Reauthorize Command');
		#Reauthorize Orders with Total Full Amount
		$ReAuthorize = new ReAuthorize();
		#Setting Production Environment
		if (Environment::is('production')) {
			$ReAuthorize->env = 'production';
		} else if (Environment::is('staging')) {
			$ReAuthorize->env = 'staging';
		}
		$ReAuthorize->fullAmount = true;
		$ReAuthorize->orders = $orders;
		$this->queue->status = "Authorizing Full Amount on orders";
		 $this->queue->percent = null;
		$this->queue->save();
		$this->log('Starting Full Reauthorize');
		$orders = $ReAuthorize->run();
		//total same until here 345pm
		if ($orders) {
			$order_total = $orders->count();
			$inc = 1;
			/**
			* Checks if this queue already started this process once before
			* If so, just continue with the same file, if not created a new file.
			**/
			$split_number = 0;
			$lines = 0;
			if ($this->queue->summary) {
               if (!empty($this->queue->summary['order']['filename'])) {
                   $filename = $this->queue->summary['order']['filename'];
                   $handle = $this->tmp.$filename;
			       $fp = fopen($handle, 'a+');
			       $this->log("Queue ran before. Appending to file $filename");
                    /**
                    * Retrieve the number of orders already processed
                    * @returns $last_order - last order entered in the file
                    * @returns $last_order_size - Number of line items
                    * already processed for that order
                    * @returns $split_number - number of orders to skip
                    **/
			       extract($this->lastOrder($fp));
			       if ($lines != 0) {
                       $this->log("The last order entered into the file was $last_order");
                       $conditions = array('order_id' => $last_order);
                       $lastOrder = $orderCollection->findOne(compact('conditions'));
                       $orderItem = count($lastOrder['items']);
                       if (($processCheck > 0) && ($processCheck == $orderItem)) {
                            $this->log("All the items was processed for the last entered order.");
                            $split_number += 1;
                       }
                       $this->log("Number of orders found " . $orders->count());
                       $this->log("Skipping the first " . $split_number ." already processed orders.");
                       $orders = $orders->skip($split_number);
                       $this->log("Remaining number of orders found " . $orders->count(true));
			       } else {
			        $this->log("Empty file");
			       }
                }
			} else {
			    $filename = 'TOTOrd'.$this->time.'.txt';
                $this->summary['order']['filename'] = $filename;
                $this->queue->summary = $this->summary;
                $this->queue->save();
                $handle = $this->tmp.$filename;
			    $fp = fopen($handle, 'w');
			}
			$orderArray = array();
			$ecounter = 0;
            $this->queue->status = "Processing Order Files";
		    $this->queue->save();
			//new counts for email breakdown
			$allitems = 0;
			$unprocessed_orders = 0;
			$unprocessed_orders_items = 0;
			$processed_orders = 0;
			$processed_orders_items = 0;
			foreach ($orders as $order) {
				$conditions = array('Customer PO #' => array('$in' => array((string) $order['_id'], $order['_id'])));
				$processCheck = ProcessedOrder::count(compact('conditions'));
				++$ecounter;

				//get items in order before check here
				$items = $order['items'];

				//total of items count to add to each subtotal
				$raw_item_count = count($items);

				//add to raw item count
				$allitems += $raw_item_count;

				if ($processCheck == 0) {

					//this is unprocessed total for orders, items
					$unprocessed_orders++;
					$unprocessed_orders_items += $raw_item_count;

					$user = User::find('first', array(
					    'conditions' => array('_id' => $order['user_id']),
					    'fields' => array('email' => true)
					    ));
					foreach ($items as $item) {
						if (empty($item['cancel']) && empty($item['digital'])) {
                                $orderItem = $itemCollection->findOne(
                                    array('_id' => new MongoId($item['item_id']))
							);
							 /**
                            * Checking if sku exists, if not find it in the item master
                            * If not in item master create one
                            **/
                             $description = implode(' ', array(
								$item['color'],
								$item['size'],
								$item['description']
							));
                            if (!array_key_exists('sku_details', $orderItem)){
                                $sku = $this->findSku(String::asciiClean($description), $item["size"]);
                                if (!($sku)) {
                                    $makeSku = new Item();
                                    $makeSku->generateSku(array($orderItem));
                                    $orderItem = Item::find('first', array(
                                        'conditions' => array('_id' => $orderItem['_id']),
                                        'fields' => array('sku_details' => true)
                                    ));
                                }
                            }
                            $sku = $orderItem['sku_details'][$item['size']];
							$orderFile[$inc]['ContactName'] = '';
							$orderFile[$inc]['Date'] = date('m/d/Y');
							if ($order['shippingMethod'] == 'ups') {
							     $orderFile[$inc]['ShipMethod'] = 'SP';
							} else {
							     $orderFile[$inc]['ShipMethod'] = $order['shippingMethod'];
							}
							$orderFile[$inc]['RushOrder (Y/N)'] = '';
							if (array_key_exists('telephone',$order['shipping'] )) {
							    $orderFile[$inc]['Tel'] = $order['shipping']['telephone'];
							} else {
							    $orderFile[$inc]['Tel'] = '';
							}
							$orderFile[$inc]['Country'] = '';
							$orderFile[$inc]['OrderNum'] = $order['order_id'];
							$orderFile[$inc]['SKU'] = $sku;
							if (empty($sku)) {
								$orderFile[$inc]['SKU'] = Item::getUniqueSku($orderItem['vendor'], $orderItem['vendor_style'], $item['size'], $item['color']);
							}
							$orderFile[$inc]['Qty'] = $item['quantity'];
							$orderFile[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
							$orderFile[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
							$orderFile[$inc]['Customer PO #'] = $order['_id'];
							$orderFile[$inc]['Pack Slip Comment'] = '';
							$orderFile[$inc]['Special Packing Instructions'] = '';
							$orderFile[$inc]['Address1'] =  str_replace(',', ' ', $order['shipping']['address']);
							if (!empty($order['shipping']['address_2'])) {
								$orderFile[$inc]['Address2'] = str_replace(',', ' ', $order['shipping']['address_2']);
							} else {
								$orderFile[$inc]['Address2'] = "";
							}
							$orderFile[$inc]['City'] = $order['shipping']['city'];
							$orderFile[$inc]['StateOrProvince'] = $order['shipping']['state'];
							$orderFile[$inc]['Zip'] = $order['shipping']['zip'];
							$orderFile[$inc]['Ref1'] = $item['item_id'];
							$orderFile[$inc]['Ref2'] = $item['size'];
							$orderFile[$inc]['Ref3'] = $item['color'];
							$orderFile[$inc]['Ref4'] = String::asciiClean($item['description']);
							$orderFile[$inc]['Customer PO #'] = $order['_id'];

							$orderFile[$inc]['Order Creation Date'] = date("m/d/Y", str_replace("0.00000000 ", "", $order['date_created']));
							$orderFile[$inc]['Promised Ship-by Date'] = date("m/d/Y", str_replace("0.00000000 ", "", $order['ship_date']));

							$orderFile[$inc] = array_merge($heading, $orderFile[$inc]);
							$orderFile[$inc] = $this->sortArrayByArray($orderFile[$inc], $heading);
							if (!in_array($item['event_id'], $this->addEvents)) {
								$this->addEvents[] = $item['event_id'];
							}
							if (!in_array($orderFile[$inc]['OrderNum'], $orderArray)) {
								$orderArray[] = $orderFile[$inc]['OrderNum'];
							}

								$processedOrder = ProcessedOrder::connection()->connection->{'orders.processed'};
								$processedOrder->save($orderFile[$inc] + $this->batchId);
							$this->log("Adding order $order[_id] to $handle");
							fputcsv($fp, $orderFile[$inc], chr(9));
							++$inc;
						}
					}
					if ($order_total != 0) {
                        $this->queue->percent = ($ecounter/$order_total) * 100;
                        $this->queue->save();
                    }
				} else {
					//if already processed, make totals of orders, items for that
					$processed_orders++;
					$processed_orders_items += $raw_item_count;
					$this->log("Already processed $order[_id]");
				}
			}
			fclose($fp);
			if (!rename($handle, $this->pending.$filename)) {
			    $this->log("Failed to move file " . $handle . " Filesize was " . filesize($handle));
			    $new_location = $this->pending.$filename;
			    $this->log("Using shell command to move file");
			    shell_exec("mv ". $handle . " " . $new_location);
			}
			$totalOrders = count($orderArray);
			//new totals for breakdown in email
			$this->summary['order']['unprocessed_orders'] = $unprocessed_orders;
			$this->summary['order']['processed_orders'] = $processed_orders;

			$this->summary['order']['unprocessed_orders_items'] = $unprocessed_orders_items;
			$this->summary['order']['processed_orders_items'] = $processed_orders_items;

			$this->summary['order']['queue_total_orders'] = $ecounter;
			$this->summary['order']['queue_total_items'] = $allitems;



			$this->summary['order']['count'] = count($orderArray) + $split_number;
			$this->summary['order']['lines'] = ($inc + $lines) - 1;
			$this->summary['order']['filename'] = $filename;
			$this->log("$handle was created total of $totalOrders orders generated with $inc lines");
		} else {
			$this->log('No orders found');
		}
		return true;
	}

	/**
	 * The itemGenerator method builds the item list for the item master database and file.
	 *
	 * The method first looks for all the events that were queued for processing. This includes
	 * the events that were associated with an order but not explicitly queued. All the
	 * items of those events are then gathered into a file for transmission to the 3PL.
	 * To avoid retransmission an item master is retained and checked before including
	 * the item in the file.
	 *
	 * @param array $eventId Array of events.
	 */
	protected function _itemGenerator() {
	    MongoCursor::$timeout = -1;
		$this->log('Generating Items');
		$filename = 'TOTIT'.$this->time.'.csv';
		$handle = $this->tmp.$filename;
		$eventIds = array_unique(array_merge($this->orderEvents, $this->poEvents, $this->addEvents));
		$event_count =  count($eventIds);
		$this->log("Total Number of Events encountered: " . $event_count);
		$this->log("Opening item file $handle");
		$fp = fopen($handle, 'w');
		$count = 0;
		if ($eventIds) {
			$productHeading = ProcessedOrder::$_productHeading;
			foreach ($eventIds as $eventId) {
				$event = Event::find('first', array(
					'conditions' => array(
						'_id' => $eventId
				)));
				$inc = 1;
				$eventItems = $this->_getOrderItems($eventId);
				$this->log("Event $eventId has " . count($eventItems) . " items");
				foreach ($eventItems as $eventItem) {
					/**
					* Checking if sku exists, if not find it in the item master
					* If not in item master create one
					**/
				    if (!array_key_exists('sku_details', $eventItem)){
				    	$this->log("Item {$eventItem['_id']} doesn't have sku_details.  Generating.");
				          $makeSku = new Item();
				          $makeSku->generateSku(array($eventItem));
                          $eventItem = Item::find('first', array(
                                'conditions' => array('_id' => $eventItem['_id']),
                                'fields' => array(
                                    'sale_whol' => true,
                                    'category' => true,
                                    'orig_whol' => true,
                                    '_id' => true,
                                    'color' => true,
                                    'product_weight' => true,
                                    'vendor_style' => true,
                                    'sku_details' => true,
                                    'description' => true,
                                    'details' => true
                                )
                            ));
				    }
					foreach ($eventItem['details'] as $key => $value) {
					    $description = implode(' ', array(
								$eventItem['color'],
								$key,
								$eventItem['description']
							));
					    $sku = $eventItem['sku_details'][$key];
						$conditions = array('SKU' => $sku, 'style' => $eventItem['vendor_style']);
						$itemMasterCheck = ItemMaster::count(compact('conditions'));
						if ($itemMasterCheck == 0){
							$fields[$inc]['SKU'] = $sku;
							if(!empty($sku)) {
								$fields[$inc]['SKU'] = Item::getUniqueSku($eventItem['vendor'], $eventItem['vendor_style'], $key, $eventItem['color']);
							}
							 
							if ($this->verbose == 'true') {
								$this->log("Adding SKU: $sku to $handle");
							}
							$fields[$inc]['Description'] = String::asciiClean($description);
							$fields[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
							$fields[$inc]['Description for Customs'] = (!empty($eventItem['category']) ? $eventItem['category'] : "");
							$fields[$inc]['ShipInsValue'] = number_format($eventItem['orig_whol'], 2);
							$fields[$inc]['Ref1'] = $eventItem['_id'];
							$fields[$inc]['Ref2'] = $key;
							$fields[$inc]['Ref3'] = $eventItem['color'];
							if ((int) $eventItem['product_weight'] > 0) {
								$fields[$inc]['UOM1_Weight'] = number_format($eventItem['product_weight'],2);
							}
							$fields[$inc]['Style'] = substr($eventItem['vendor_style'], 0, 15);
							$fields[$inc] = array_merge($productHeading, $fields[$inc]);
							$productFile[$inc] = $this->sortArrayByArray($fields[$inc], $productHeading);
						}
						if (!empty($productFile[$inc])) {
							if ($this->test != 'true') {
								$itemMasterEntry = ItemMaster::create();
								$itemMasterEntry->save($productFile[$inc] + $this->batchId);
								++$count;
							}
							fputcsv($fp, $productFile[$inc]);
						}
					}
					++$inc;
				}
			    if ($event_count != 0) {
                    $this->queue->percent = (float)number_format((($inc/$event_count) * 100), 2);
                    $this->queue->save();
                }
			}
		}
		fclose($fp);
		if ( !rename($handle, $this->pending.$filename) ){
		    $this->log("Failed to move file " . $handle . " File size was " . filesize($handle));
		    shell_exec("mv " . $handle . " " . $this->pending.$filename);
		}
		$this->summary['item']['count'] = $count;
		$this->summary['item']['filename'] = $filename;
		$this->log("There were $count items generated and saved to the item master and $handle");
		return true;
	}

	/**
	 * The purchases method generates the PO report for the logistics team. This report returns an associative array
	 * which lists all the sales of each item of a sale.
	 *
	 * The order of operation is as follows:
	 *
	 * 1) Find all the event that is being requested in the eventList array.
	 * 2) Find all the items that are a part of the event requested.
	 * 3) For each item get all the orders that have been placed with that item in it.
	 * 4) Build the array of cumulative purchases for each item of the event.
	 * @return mixed
	 */
	protected function _purchases() {
	     MongoCursor::$timeout = -1;
		$this->log('Generating Purchase Orders');
		$orderCollection = Order::collection();
		$event_count = count($this->poEvents);
		$ecount = 0;
		foreach ($this->poEvents as $eventId) {
			$purchaseHeading = ProcessedOrder::$_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->_getOrderItems($eventId);
			$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
			$time = date('ymdis', $event->_id->getTimestamp());
			$poNumber = 'TOT'.'-'.$vendorName.$time;
			$filename = 'TOTitpo'.$vendorName.$time.'.csv';
			$handle = $this->tmp.$filename;
			$this->log("Opening PO file $handle");
			$fp = fopen($handle, 'w');
			$this->summary['purchase_orders'][] = $filename;
			
			$purchaseOrder = array();
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				foreach ($eventItem['details'] as $key => $value) {
					$this->log("{$eventItem['_id']} size {$key} value {$value}");
					$orders = $orderCollection->find( array(
						'items.item_id' => (string) $eventItem['_id'],
						'items.size' => (string) $key,
						'items.status' => array('$ne' => 'Order Canceled'),
						'cancel' => array('$ne' => true))
					);
					if ($orders) {
						foreach ($orders as $order) {
							$items = $order['items'];
							$date_created = $order['date_created'];
							foreach ($items as $item) {
								$active = (empty($item['cancel']) || $item['cancel'] != true) ? true : false;
								$itemValid = ($item['item_id'] == $eventItem['_id']) ? true : false;
								if ($itemValid && ((string) $key == $item['size']) && $active){
									$purchaseOrder[$inc]['Supplier'] = $eventItem['vendor'];
									$purchaseOrder[$inc]['PO # / RMA #'] = $poNumber;
									$purchaseOrder[$inc]['SKU'] = $eventItem['sku_details'][$item['size']];
									if (empty($eventItem['sku_details'][$item['size']])) {
										$purchaseOrder[$inc]['SKU'] = Item::getUniqueSku($eventItem['vendor'], $eventItem['vendor_style'], (string)$item['size'], $item['color']);
									}
									if (empty($purchaseOrder[$inc]['Qty'])) {
										$purchaseOrder[$inc]['Qty'] = $item['quantity'];
									} else {
										$purchaseOrder[$inc]['Qty'] += $item['quantity'];
									}
									//new additions
									$purchaseOrder[$inc]['Vendor Style'] = $eventItem['vendor_style'];
									$purchaseOrder[$inc]['Vendor Name'] = $vendorName;
									$purchaseOrder[$inc]['Item Color'] = $item['color'];
									$purchaseOrder[$inc]['Item Size'] = $item['size'];
									$purchaseOrder[$inc]['Item Description'] = $eventItem['description'];
									$purchaseOrder[$inc]['Order Creation Date'] = date("m/d/Y", str_replace("0.00000000 ", "", $order['date_created']));
									$purchaseOrder[$inc]['Promised Ship-by Date'] = date("m/d/Y", str_replace("0.00000000 ", "", $order['ship_date']));
									$purchaseOrder[$inc]['Event Name'] = $event->name;
									$purchaseOrder[$inc]['Event End Date'] = date("m/d/Y", str_replace("0.00000000 ", "", $event->end_date));


							$purchaseOrder[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
							$purchaseOrder[$inc]['Description for Customs'] = (!empty($eventItem['category']) ? $eventItem['category'] : "");
							$purchaseOrder[$inc]['ShipInsValue'] = number_format($eventItem['orig_whol'], 2);
							$purchaseOrder[$inc]['Ref1'] = $eventItem['_id'];
							$purchaseOrder[$inc]['Ref2'] = $key;
							$purchaseOrder[$inc]['Ref3'] = $eventItem['color'];


							if ((int) $eventItem['product_weight'] > 0) {
								$purchaseOrder[$inc]['UOM1_Weight'] = number_format($eventItem['product_weight'],2);
							}

									$purchaseOrder[$inc] = $this->sortArrayByArray($purchaseOrder[$inc], $purchaseHeading);
								}
							}
						}
						if (!empty($purchaseOrder[$inc])) {
							fputcsv($fp, array_merge($purchaseHeading, $purchaseOrder[$inc]));
							if ($this->test != 'true') {
								$po = PurchaseOrder::create();
								$po->save(array_merge($purchaseHeading, $purchaseOrder[$inc]) + $this->batchId);
							}
						}
						++$inc;
					}
				}
			}
			++$ecount;
			if ($event_count != 0) {
                $this->queue->percent = (float)number_format((($ecount/$event_count) * 100), 2);
                $this->queue->save();
            }
			fclose($fp);
			rename($handle, $this->pending.$filename);
		}
	}

	/**
	* Find the sku of an item
	* returns the sku if it is found or false if otherwise
	**/
	protected function findSku($description, $size) {
	    $conditions = array('Description' => $description, 'Ref2' => $size );
	    $item_master = ItemMaster::collection()->findOne($conditions, array('SKU' => true));

	    if ( $item_master) {
	        return $item_master['SKU'];
	    } else {
	        return false;
	    }
	}

	/**
	* Retrieve last order entered in order file
	* @returns $last_order - last order entered in the file
    * @returns $last_order_size - Number of line items
    * already processed for that order
    * @returns $split_number - number of orders to skip
	**/
	protected function lastOrder($openFile) {
	    $orders = array();
	    $lines = 0;
	    while( ($data = fgetcsv($openFile,0,"\t")) != FALSE) {
	            ++$lines;
	            $orders[$data[5]][] = $data[6];
	    }
	    if (count($orders) != 0 ) {
            $orders = array_reverse($orders);
            $last_order = key($orders);
            $last_order_size = count($orders[$last_order]);
            /**
            * This count will not include the last order
            **/
            $split_number = count($orders) - 1;
	    } else {
	        $last_order = null;
            $last_order_size = 0;
             /**
            * This count will not include the last order
            **/
            $split_number = 0;
	    }

	    return compact('last_order','last_order_size','split_number', 'lines');
	}

	/**
	 * Return all the items of an event.
	 */
	protected function _getOrderItems($eventId = null) {
		$itemCollection = Item::collection();
		$items = null;
		if ($eventId) {
			$items = $itemCollection->find(array(
					'event' => array('$in' => array($eventId)
					)));
		}
		return $items;
	}

}
?>
