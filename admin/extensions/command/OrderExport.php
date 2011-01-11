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
 */
class OrderExport extends Base {

	public function run() {
		$this->header('Exporting Orders');
		$ordersExported = $this->_orderGenerator();
		//$this->out("There were $this->orderCount orders exported totalling $this->lineCount");
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
		$this->batchId = array('order_batch' => substr(md5(uniqid(rand(),1)), 1, 20));
		$heading = ProcessedOrder::$_fileHeading;
		$events = array('4d0fa0c8ce64e58f57d72000');
		$orders = $orderCollection->find(array('items.event_id' => array('$in' => $events)));
		$inc = 0;
		$this->time = date('Ymd', time());
		$fp = fopen('/tmp/TOTOrd'.$this->time.'_2.txt', 'w');
		$eventList = array();
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
					fputcsv($fp, $orderFile[$inc], chr(9));
					$processedOrder = ProcessedOrder::create();
					$processedOrder->save($orderFile[$inc] + $this->batchId);
					++$inc;
				}
			}
		}
		fclose($fp);
		$this->_itemGenerator($eventList);
		$this->_purchases($eventList);
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
		$fp = fopen('/tmp/TOTIT'.$this->time.'_2.csv', 'w');
		if ($eventIds) {
			$productHeading = ProcessedOrder::$_productHeading;
			foreach ($eventIds as $eventId) {
				$event = Event::find('first', array(
					'conditions' => array(
						'_id' => $eventId
				)));
				$inc = 0;
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
							$itemMasterEntry = ItemMaster::create();
							$itemMasterEntry->save($productFile[$inc] + $this->batchId);
							fputcsv($fp, $productFile[$inc]);
						}
						++$inc;
					}
				}
			}
		}
		fclose($fp);
		return true;
	}

	/**
	 * The purchases method generates the PO report for the logistics team. This report returns an associative array
	 * which lists all the sales of each item of a sale.
	 *
	 * The order of operation is as follows:
	 *
	 * 1) Find all the event that is being requested via the URL.
	 * 2) Find all the times that are a part of the event requested.
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
			$inc = 0;
			$vendorName = trim(substr($event->name, 0, 5), ' ');
			$fp = fopen('/tmp/TOTitpo'.$vendorName.$this->time.'.csv', 'w');
			$purchaseOrder = array();
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
										$purchaseOrder[$inc]['PO # / RMA #'] = $vendorName.'-'.$this->time;
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
										$purchaseOrder[$inc] = array_merge($purchaseHeading, $purchaseOrder[$inc]);
										$purchaseOrder[$inc] = $this->sortArrayByArray($purchaseOrder[$inc], $purchaseHeading);
									}
								}
							}
							$po = PurchaseOrder::create();
							$po->save($purchaseOrder[$inc] + $this->batchId);
							fputcsv($fp, $purchaseOrder[$inc]);
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