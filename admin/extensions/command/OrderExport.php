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
use admin\extensions\command\Base;
use admin\extensions\command\Exchanger;
use lithium\analysis\Logger;
use li3_silverpop\extensions\Silverpop;
use admin\extensions\util\String;
use admin\extensions\command\Pid;

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
	 * Main method for exporting Order and PO files.
	 *
	 * The `run` method will query the pending event transactions
	 * that have not yet been processed. This queuing system will be managed
	 * from the admin dashboard.
	 *
	 * @todo Remove the environment set to the base command.
	 * @todo Make sure a queue cannot contain two empty arrays.
	 */
	public function run() {
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$this->processed = LITHIUM_APP_PATH . $this->processed;
		$this->pending = LITHIUM_APP_PATH . $this->pending;
		$this->log("...Waking up...");
		$pid = new Pid($this->tmp,  'OrderExport');
		if ($pid->already_running == false) {
			$conditions = array('processed' => array('$ne' => true));
			$records = Queue::find('all', compact('conditions'));
			foreach ($records as $queue) {
				$this->summary = array();
				$this->log("Processing Queue Record: $queue->_id");
				if ($queue) {
					$this->batchId = array('order_batch' => $queue->_id);
					$this->log("Starting to process $queue->_id");
					$this->time = date('Ymdis');
					$queueData = $queue->data();
					if ($queueData['orders']) {
						$this->orderEvents = $queueData['orders'];
						$this->_orderGenerator();
					}
					if ($queueData['purchase_orders']) {
						$this->poEvents = $queueData['purchase_orders'];
						$this->_purchases();
					}
					$this->_itemGenerator();
					if ($queueData['orders'] || $queueData['purchase_orders']) {
						$queue->summary = $this->summary;
						$queue->processed = true;
						$queue->processed_date = new MongoDate();
						$queue->save();
						$this->summary['from_email'] = 'no-reply@totsy.com';
						$this->summary['to_email'] = 'logistics@totsy.com';
						Silverpop::send('exportSummary', $this->summary);
					}
				}
			}
		} else {
			$this->log("Already Running! Stoping Execution");
		}
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
		$this->log("Starting to process Orders");
		$orderCollection = Order::collection();
		$itemCollection = Item::connection()->connection->items;
		$orderFile = array();
		$heading = ProcessedOrder::$_fileHeading;
		$orders = $orderCollection->find(array(
			'items.event_id' => array('$in' => $this->orderEvents),
			'cancel' => array('$ne' => true)
		));
		if ($orders) {
			$inc = 1;
			$filename = 'TOTOrd'.$this->time.'.txt';
			$handle = $this->tmp.$filename;
			$fp = fopen($handle, 'w');
			$orderArray = array();
			foreach ($orders as $order) {
				$conditions = array('Customer PO #' => array('$in' => array((string) $order['_id'], $order['_id'])));
				$processCheck = ProcessedOrder::count(compact('conditions'));
				if ($processCheck == 0) {
					$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
					$items = $order['items'];
					foreach ($items as $item) {
						if (empty($item['cancel']) || $item['cancel'] != true) {
							$orderItem = $itemCollection->findOne(array('_id' => new MongoId($item['item_id'])));
							$orderFile[$inc]['ContactName'] = '';
							$orderFile[$inc]['Date'] = date('m/d/Y');
							if ($order['shippingMethod'] == 'ups') {
							     $orderFile[$inc]['ShipMethod'] = 'SP';
							} else {
							     $orderFile[$inc]['ShipMethod'] = $order['shippingMethod'];
							}
							$orderFile[$inc]['RushOrder (Y/N)'] = '';
							$orderFile[$inc]['Tel'] = $order['shipping']['telephone'];
							$orderFile[$inc]['Country'] = '';
							$orderFile[$inc]['OrderNum'] = $order['order_id'];
							$orderFile[$inc]['SKU'] = Item::sku(
								$orderItem['vendor'],
								$orderItem['vendor_style'],
								$item['size'],
								$orderItem['color']
							);
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
							$orderFile[$inc] = array_merge($heading, $orderFile[$inc]);
							$orderFile[$inc] = $this->sortArrayByArray($orderFile[$inc], $heading);
							if (!in_array($item['event_id'], $this->addEvents)) {
								$this->addEvents[] = $item['event_id'];
							}
							if (!in_array($orderFile[$inc]['OrderNum'], $orderArray)) {
								$orderArray[] = $orderFile[$inc]['OrderNum'];
							}
							if ($this->test != 'true') {
								$processedOrder = ProcessedOrder::connection()->connection->{'orders.processed'};
								$processedOrder->save($orderFile[$inc] + $this->batchId);
							}
							$this->log("Adding order $order[_id] to $handle");
							fputcsv($fp, $orderFile[$inc], chr(9));
							++$inc;
						}
					}
				} else {
					$this->log("Already processed $order[_id]");
				}
			}
			fclose($fp);
			rename($handle, $this->pending.$filename);
			$totalOrders = count($orderArray);
			$this->summary['order']['count'] = count($orderArray);
			$this->summary['order']['lines'] = $inc;
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
		$this->log('Generating Items');
		$filename = 'TOTIT'.$this->time.'.csv';
		$handle = $this->tmp.$filename;
		$eventIds = array_unique(array_merge($this->orderEvents, $this->poEvents, $this->addEvents));
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
				foreach ($eventItems as $eventItem) {
					foreach ($eventItem['details'] as $key => $value) {
						$sku = Item::sku($eventItem['vendor'], $eventItem['vendor_style'], $key, $eventItem['color']);
						$conditions = array('SKU' => $sku);
						$itemMasterCheck = ItemMaster::count(compact('conditions'));
						if ($itemMasterCheck == 0){
							$fields[$inc]['SKU'] = $sku;
							if ($this->verbose == 'true') {
								$this->log("Adding SKU: $sku to $handle");
							}
							$description = implode(' ', array(
								$eventItem['color'],
								$key,
								$eventItem['description']
							));
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
						++$inc;
					}
				}
			}
		}
		fclose($fp);
		rename($handle, $this->pending.$filename);
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
		$this->log('Generating Purchase Orders');
		$orderCollection = Order::collection();
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
					$orders = $orderCollection->find(array(
						'items.item_id' => (string) $eventItem['_id'],
						'items.size' => (string) $key,
						'items.status' => array('$ne' => 'Order Canceled'),
						'cancel' => array('$ne' => true))
					);
					if ($orders) {
						foreach ($orders as $order) {
							$items = $order['items'];
							foreach ($items as $item) {
								$active = (empty($item['cancel']) || $item['cancel'] != true) ? true : false;
								$itemValid = ($item['item_id'] == $eventItem['_id']) ? true : false;
								if ($itemValid && ((string) $key == $item['size']) && $active){
									$purchaseOrder[$inc]['Supplier'] = $eventItem['vendor'];
									$purchaseOrder[$inc]['PO # / RMA #'] = $poNumber;
									$purchaseOrder[$inc]['SKU'] = Item::sku(
										$eventItem['vendor'],
										$eventItem['vendor_style'],
										$item['size'],
										$eventItem['color']
									);
									if (empty($purchaseOrder[$inc]['Qty'])) {
										$purchaseOrder[$inc]['Qty'] = $item['quantity'];
									} else {
										$purchaseOrder[$inc]['Qty'] += $item['quantity'];
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
			fclose($fp);
			rename($handle, $this->pending.$filename);
		}
	}

	/**
	 * Return all the items of an event.
	 */
	protected function _getOrderItems($eventId = null) {
		$items = null;
		if ($eventId) {
			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			))));
			$items = $items->data();
		}
		return $items;
	}

}