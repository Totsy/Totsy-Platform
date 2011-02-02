<?php

namespace admin\extensions\command;

use admin\models\ProcessedOrder;
use admin\models\Item;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\ItemMaster;
use admin\models\PurchaseOrder;
use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use admin\extensions\command\Base;



/**
 * Export Order, Item and PO files to DC system.
 */
class OrderExport extends Base {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'production';

	/**
	 * The test boolean string flag will prevent saves to database. 'false' is the default.
	 *
	 * @var boolean
	 */
	public $test = false;

	/**
	 * FTP Server of 3PL we are sending files to.
	 *
	 * @var string
	 */
	protected $_ftpServer = 'ftp.dotcomdistribution.com';

	/**
	 * FTP User Name.
	 *
	 * @var string
	 */
	protected $_ftpUser = 'TOT90';

	/**
	 * FTP Password.
	 *
	 * @var string
	 */
	protected $_ftpPass = '4J518t54';

	/**
	 * The array list of events that should be batched
	 * processed.
	 *
	 * @var array
	 */
	protected $events = array();

	/**
	 * Any files that should be excluded during import.
	 *
	 * @var array
	 */
	protected $_exclude = array(
		'.',
		'..',
		'.DS_Store',
		'processed'
	);

	/**
	 * Directory of files holding the files to FTP.
	 *
	 * @var string
	 */
	public $source = '/tmp/totsy';

	/**
	 * Directory of files holding the backup files to FTP.
	 *
	 * @var string
	 */
	public $processed = '/tmp/totsy/processed';

	/**
	 * Full path to file.
	 */
	protected $path = null;

	/**
	 * Main method for exporting Order and PO files.
	 *
	 * The `run` method will query the pending event transactions
	 * that have not yet been processed. This queuing system will be managed
	 * from the admin dashboard.
	 *
	 * @todo Remove the environment set to the base command.
	 * @todo Build query to gather events from DB as a queue.
	 */
	public function run() {
		Environment::set($this->env);
		$this->events = array(
		);
		if ($this->events) {
			$this->batchId = array('order_batch' => substr(md5(uniqid(rand(),1)), 1, 20));
			$batch = $this->batchId['order_batch'];
			$this->time = date('Ymdis');
			$this->out("Generating orders under batch# $batch");
			$this->_orderGenerator();
			$this->_purchases();
			$this->_export();
		} else {
			$this->out('No events in queue for processing');
		}
	}

	/**
	 * The Order Generator
	 * this method will be migrated to a command method and executed via cron job.
	 */
	public function _orderGenerator() {
		$this->header('Generating Orders');
		$orderCollection = Order::collection();
		$orderFile = array();
		$heading = ProcessedOrder::$_fileHeading;
		$orders = $orderCollection->find(array(
			'items.event_id' => array('$in' => $this->events),
			'canceled' => array('$ne' => true)
		));
		if ($orders) {
			$inc = 1;
			$handle = '/tmp/totsy/TOTOrd'.$this->time.'.txt';
			$fp = fopen($handle, 'w');
			$eventList = $orderArray = array();
			foreach ($orders as $order) {
				$conditions = array('Customer PO #' => array('$in' => array((string) $order['_id'], $order['_id'])));
				$processCheck = ProcessedOrder::count(compact('conditions'));
				if ($processCheck == 0) {
					$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
					$items = $order['items'];
					foreach ($items as $item) {
						$orderItem = Item::find('first', array(
							'conditions' => array(
								'_id' => $item['item_id']
						)));
						$orderFile[$inc]['ContactName'] = '';
						$orderFile[$inc]['Date'] = date('m/d/Y');
						if ($order['shippingMethod'] == 'ups') {
						     $orderFile[$inc]['ShipMethod'] = 'UPSGROUND';
						} else {
						     $orderFile[$inc]['ShipMethod'] = $order['shippingMethod'];
						}
						$orderFile[$inc]['RushOrder (Y/N)'] = '';
						$orderFile[$inc]['Tel'] = $order['shipping']['telephone'];
						$orderFile[$inc]['Country'] = '';
						$orderFile[$inc]['OrderNum'] = $order['order_id'];
						$orderFile[$inc]['SKU'] = Item::sku($orderItem->vendor, $orderItem->vendor_style, $item['size'], $orderItem->color);
						$orderFile[$inc]['Qty'] = $item['quantity'];
						$orderFile[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
						$orderFile[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
						$orderFile[$inc]['Customer PO #'] = $order['_id'];
						$orderFile[$inc]['Pack Slip Comment'] = '';
						$orderFile[$inc]['Special Packing Instructions'] = '';
						$orderFile[$inc]['Address1'] =  str_replace(',', ' ', $order['shipping']['address']);
						$orderFile[$inc]['Address2'] = str_replace(',', ' ', $order['shipping']['address_2']);
						$orderFile[$inc]['City'] = $order['shipping']['city'];
						$orderFile[$inc]['StateOrProvince'] = $order['shipping']['state'];
						$orderFile[$inc]['Zip'] = $order['shipping']['zip'];
						$orderFile[$inc]['Ref1'] = $item['item_id'];
						$orderFile[$inc]['Ref2'] = $item['size'];
						$orderFile[$inc]['Ref3'] = $item['color'];
						$orderFile[$inc]['Ref4'] = $this->_asciiClean($item['description']);
						$orderFile[$inc]['Customer PO #'] = $order['_id'];
						$orderFile[$inc] = array_merge($heading, $orderFile[$inc]);
						$orderFile[$inc] = $this->sortArrayByArray($orderFile[$inc], $heading);
						if (!in_array($item['event_id'], $eventList)) {
							$eventList[] = $item['event_id'];
						}
						if (!in_array($orderFile[$inc]['OrderNum'], $orderArray)) {
							$orderArray[] = $orderFile[$inc]['OrderNum'];
						}
						if ($this->test != 'true') {
							$processedOrder = ProcessedOrder::create();
							$processedOrder->save($orderFile[$inc] + $this->batchId);
						}
						$this->out("Adding order $order[_id] to $handle");
						fputcsv($fp, $orderFile[$inc], chr(9));
						++$inc;
					}
				}
			}
			fclose($fp);
			$this->_itemGenerator($eventList);
			$totalOrders = count($orderArray);
			$this->out("$handle was created total of $totalOrders orders generated with $inc lines");
		} else {
			$this->out('No orders found');
		}
		return true;
	}

	/**
	 * Take any string and convert to ASCII
	 */
	protected function _asciiClean($description) {
		return preg_replace('/[^(\x20-\x7F)]*/','', $description);
	}

	/**
	 * The itemGenerator method builds the item list for the item master database and file.
	 *
	 * The method first looks for all the events that were queued for processing. All the
	 * items of those events are then gathered into a file for transmission to the 3PL.
	 * To avoid retransmission an item master is retained and checked before including
	 * the item in the file.
	 *
	 * @param array $eventId Array of events.
	 */
	protected function _itemGenerator($eventIds = null) {
		$this->header('Generating Items');
		$handle = '/tmp/totsy/TOTIT'.$this->time.'.csv';
		$this->out("Opening item file $handle");
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
							$description = implode(' ', array(
								$eventItem['color'],
								$key,
								$eventItem['description']
							));
							$fields[$inc]['Description'] = $this->_asciiClean($description);
							$fields[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
							$fields[$inc]['Description for Customs'] = $eventItem['category'];
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
		$this->out("There were $count items generated and saved to the item master and $handle");
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
		$this->header('Generating Purchase Orders');
		foreach ($this->events as $eventId) {
			$purchaseHeading = ProcessedOrder::$_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->_getOrderItems($eventId);
			$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr($this->_asciiClean($event->name), 0, 3));
			$time = date('ymdis', $event->_id->getTimestamp());
			$poNumber = 'TOT'.'-'.$vendorName.$time;
			$handle = '/tmp/totsy/TOTitpo'.$vendorName.$time.'.csv';
			$this->out("Opening PO file $handle");
			$fp = fopen($handle, 'w');
			$purchaseOrder = array();
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				foreach ($eventItem['details'] as $key => $value) {
					$orders = Order::find('all', array(
						'conditions' => array(
							'items.item_id' => (string) $eventItem['_id'],
							'items.size' => (string) $key,
							'items.status' => array('$ne' => 'Order Canceled')
					)));
					if ($orders) {
						$orderData = $orders->data();
						if (!empty($orderData)) {
							foreach ($orderData as $order) {
								$items = $order['items'];
								foreach ($items as $item) {
									if (($item['item_id'] == $eventItem['_id']) && ((string) $key == $item['size'])){
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
								$po = PurchaseOrder::create();
								$po->save(array_merge($purchaseHeading, $purchaseOrder[$inc]) + $this->batchId);
							}
							++$inc;
						}
					}
				}
			}
			fclose($fp);
		}
	}

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
	/**
	 * This export script examine the source directory and send any files
	 * that have not already been transmitted. Once the transmission has been
	 * confirmed move the file over to a backup folder within the same directory.
	 */
	public function _export() {
		$this->header('FTPing Files');
		$ftpConn = ftp_connect($this->_ftpServer);
		$login = ftp_login($ftpConn, $this->_ftpUser, $this->_ftpPass);
		if ((!$ftpConn) || (!$login)) {
			$this->error("FTP Connection Failed");
		} else {
			$this->out("Connected to $this->_ftpServer");
			ftp_chdir($ftpConn, '/tot90/in');
			if ($this->source) {
				$handle = opendir($this->source);
				while (false !== ($this->file = readdir($handle))) {
					if (!(in_array($this->file, $this->_exclude))) {
						$fullPath = implode('/', array($this->source, $this->file));
						$backupPath = implode('/', array($this->processed, $this->file));
						if (filesize($fullPath) > 0) {
							if (ftp_put($ftpConn, $this->file, $fullPath, FTP_BINARY)) {
								$this->out("Successfully uploaded $this->file");
								$this->out("Moving $fullPath to $backupPath");
								rename($fullPath, $backupPath);
							} else {
								$this->error("There was a problem while uploading $this->file");
							}
						} else {
							$this->out("$fullPath was empty. Removing...");
							unlink($fullPath);
						}
					}
				}
			}
		}
		ftp_close($ftpConn);
	}

}