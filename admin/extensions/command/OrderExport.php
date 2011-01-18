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
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use admin\extensions\command\Base;



/**
 * Export orders already processed.
 *
 * @todo Include in the query for orders a boolean check for cancled.
 * We are going to need to change all Order Status = Cancled to cancled = true.
 * This is going to make life easier for everyone down the line.
 */
class OrderExport extends Base {

	public function run() {
		$this->header('Exporting Orders');
		$this->events = array(
			'4cf93ed5ce64e5660dde2d00',
			'4cfd132dce64e56c09922600',
			'4d016768ce64e55d76c30a00',
			'4d013012ce64e5ff6f120600',
			'4cffdf81ce64e54d47310400',
			'4d067533ce64e5fe24712800',
			'4d07ce69ce64e5df53bb0a00',
			'4d0802ccce64e5f159533600',
			'4d08054fce64e5c45a7f2900',
			'4d078626ce64e56f4b161200',
			'4cfd822bce64e5cc36c92c00',
			'4d0a921fce64e5782e421600',
			'4d0bbd09ce64e5e653fd1600',
			'4d0aa18ace64e5b52e946400',
			'4d0aa694ce64e5e8317c0200',
			'4d0f8de1ce64e55b54533800',
			'4d0f92c5ce64e58a56cd0600',
			'4d0fa0c8ce64e58f57d72000',
			'4d0fb4fbce64e58563c51900',
			'4d136a78ce64e50476c32000',
			'4d11034ace64e513276f3b00',
			'4d113988ce64e5eb2efe3700',
			'4d07d440ce64e51654f50b00',
			'4d12bc12ce64e5f461a20100',
			'4d12bd49ce64e5ed61820a00',
			'4d13d350ce64e5cc7c0d8200',
			'4d12c12fce64e5f461352100',
			'4d1a4b2fce64e5c370291500',
			'4d1a677dce64e58874020100',
			'4cffd7f9ce64e50c445c2700',
			'4cf9466bce64e58d0ed71d00',
			'4d0bda7fce64e52a56712900',
			'4d0f8b80ce64e52e550c3500',
			'4d13ce28ce64e57801693200',
			'4d0bd440ce64e52a561a1900',
			'4d06b42dce64e55b2c8f1600'
		);
		if (empty($this->test)) {
			$this->test = false;
		}
		//$ordersExported = $this->_orderGenerator();
		$this->batchId = array('order_batch' => substr(md5(uniqid(rand(),1)), 1, 20));
		$this->_purchases($this->events);
	}

	/**
	 * This adhoc method was used to generate the order file for dotcom. In the very near future
	 * this method will be migrated to a command method and executed via cron job.
	 *
	 *
	 *  @todo Migrate code to li3 command.
	 */
	public function _orderGenerator() {
		$orderCollection = Order::collection();
		$orderFile = array();
		$heading = ProcessedOrder::$_fileHeading;
		$orders = $orderCollection->find(array('items.event_id' => array('$in' => $this->events)));
		$inc = 1;
		$this->time = date('Ymds');
		$handle = '/tmp/totsy/TOTOrd'.$this->time.'.txt';
		$fp = fopen($handle, 'w');
		$eventList = $orderArray = array();
		foreach ($orders as $order) {
			$conditions = array('Customer PO #' => (string) $order['_id']);
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
					$orderFile[$inc]['SKU'] = Item::sku($orderItem->vendor, $orderItem->vendor_style, $item['size'], $item['color']);
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
	}
	
	protected function _asciiClean($description) {
		return preg_replace('/[^(\x20-\x7F)]*/','', $description);
	}

	/**
	 * The itemGenerator method gathers all the items to generate the order file.
	 *
	 * The purpose of this method is to create the item list for our 3PL. For every order that was created a
	 * CSV line is generated. This method needs to be simplified so that it doesnt loop through
	 * every order but builds and item list soely on what is saved in the event.
	 * Event if we don't have purchases for particular items the item file should contain everything.
	 * that we are attempting to sell. The PO will contain the details of the items and quantites
	 * that have been purchased.
	 * @todo Migrate code to li3 command.
	 */
	protected function _itemGenerator($eventIds = null) {
		$fp = fopen('/tmp/totsy/TOTIT'.$this->time.'.csv', 'w');
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
								$eventItem['description'],
								$eventItem['color'],
								$key
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
							}
							fputcsv($fp, $productFile[$inc]);
						}
						++$inc;
					}
				}
			}
		}
		fclose($fp);
		$this->out("There were $inc items generated and saved to the item master");
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
	protected function _purchases($eventList = array()) {
		foreach ($eventList as $eventId) {
			$purchaseHeading = ProcessedOrder::$_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->_getOrderItems($eventId);
			$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr($this->_asciiClean($event->name), 0, 3));
			$time = date('ymds');
			$poNumber = 'TOT'.'-'.$vendorName.$time;
			$fileHandle = '/tmp/totsy/TOTitpo'.$vendorName.$time.'.csv';
			$this->out("Opening $fileHandle");
			$fp = fopen($fileHandle, 'w');
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
}