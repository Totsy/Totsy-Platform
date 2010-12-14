<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Base;
use admin\models\Report;
use MongoCode;
use MongoDate;
use MongoRegex;
use li3_flash_message\extensions\storage\FlashMessage;

/**
 * The Reports Controller is the core for all reporting functionality.
 */
class ReportsController extends BaseController {

	/**
	 * The purchase order column headings.
	 *
	 * @var array
	 */
	protected $_purchaseHeading = array(
		'SKU',
		'Product Name',
		'Product Color',
		'Quantity',
		'Size',
		'Unit',
		'Total'
	);

	protected $_clientId = 'TOT';

	/**
	 * The product file report column heading and default values.
	 *
	 * @var array
	 */
	protected $_productHeading = array(
		'ClientID' => 'TOT',
		'SKU' => null,
		'Description' => null,
		'WhsInsValue (Cost)' => null,
		'ShipInsValue' => null,
		'Expiration_Date' => null,
		'UPC' => null,
		'Description for Customs' => null,
		'HSC Code' => null,
		'Class for LTL' => null,
		'Country of Origin' => 'USA',
		'Velocity' => 'B',
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Ref5' => null,
		'UOM1' => 'EA',
		'UOM1_Qty' => 1,
		'UOM1_Weight' => '1.00',
		'UOM1_Length' => '1.00',
		'UOM1_Width' => '1.00',
		'UOM1_Height' => '1.00',
		'UOM1_Cube' => '1.00'
	);

	protected $_orderHeading = array(
		'Select',
		'OrderNum',
		'SKU',
		'Qty',
		'CompanyOrName',
		'Email',
		'Note'
	);

	/**
	 * The order file column heading.
	 *
	 * @var	array
	 */
	protected $_fileHeading = array(
		'Date' => null,
		'ClientId' => 'TOT',
		'DC' => 'ALN',
		'ShipMethod' => null,
		'RushOrder (Y/N)' => null,
		'OrderNum' => null,
		'SKU' => null,
		'Qty' => null,
		'CompanyOrName' => null,
		'ContactName' => null,
		'Address1' => null,
		'Address2' => null,
		'City' => null,
		'StateOrProvince' => null,
		'Zip' => null,
		'Country' => null,
		'Email' => null,
		'Tel' => null,
		'Customer PO #' => null,
		'Pack Slip Comment' => null,
		'Special Packing Instructions' => null,
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Ref5' => null,
		'Ref6' => null,
		'Ref7' => null,
		'Ref8' => null,
		'Ref9' => null,
		'Ref10' => null,
		'BillType (R/3P)' => null,
		'R3PAccountNum' => null,
		'Billing CompanyName' => null,
		'Billing Address1' => null,
		'Billing Address2' => null,
		'Billing City' => null,
		'Billing State' => null,
		'Billing Country' => null,
		'Billing Telephone' => null,
		'COD (Y/N)' => null,
		'Order COD Value' => null,
		'COD: Require Payment By Cashier\'s Check/Money Order (Y/N)' => null,
		'COD: Add Shipping Costs to COD Amount (Y/N)' => null
	);


	public function index() {

	}

	public function affiliate() {
		$search = Report::create($this->request->data);
		if ($this->request->data) {
			$criteria = $this->request->data;
			$name = $this->request->data['affiliate'];
			$affiliate = new MongoRegex("/$name/i");
			if ($this->request->data['min_date'] && $this->request->data['max_date']) {
				$min = new MongoDate(strtotime($this->request->data['min_date']));
				$max = new MongoDate(strtotime($this->request->data['max_date']));
				$date = array(
					'created_date' => array(
						'$gte' => $min,
						'$lte' => $max)
				);
				$searchType = $this->request->data['search_type'];

				switch ($searchType) {
					case 'Revenue':
						$users = User::find('all', array(
							'conditions' => array(
								'invited_by' => $affiliate,
								'purchase_count' => array('$gt' => 1)
						)));
						if ($users) {
							$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
							$collection = Report::collection();
							foreach ($users as $user) {
								$orders = Order::find('all', array(
									'conditions' => array(
										'user_id' => (string) $user->_id,
										'date_created' => array(
											'$gte' => $min,
											'$lte' => $max
								))));
								$orders = $orders->data();
								if ($orders) {
									foreach ($orders as $order) {
										$order['date_created'] = new MongoDate($order['date_created']['sec']);
										$collection->save(array('data' => $order, 'report_id' => $reportId));
									}
								}
							}
						}
						$keys = new MongoCode("function(doc){return {'Date': doc.data.date_created.getMonth()}}");
						$inital = array('total' => 0);
						$reduce = new MongoCode('function(doc, prev){
							prev.total += doc.data.total
							}'
						);
						$conditions = array('report_id' => $reportId);
						$results = $collection->group($keys, $inital, $reduce, $conditions);
						$collection->remove($conditions);
						break;
					case 'Registrations':
						switch ($name) {
							case 'trendytogs':
								$conditions = array(
									'trendytogs_signup' => array('$exists' => true)
								);
								$dateField = 'date_created';
								break;
							default:
								$conditions = array(
									'invited_by' => $affiliate,
								);
								$dateField = 'created_date';
								if (!empty($date)) {
									$conditions = $conditions + $date;
								}
							$keys = new MongoCode("function(doc){return {'Date': doc.$dateField.getMonth()}}");
							$inital = array('total' => 0);
							$reduce = new MongoCode('function(doc, prev){prev.total += 1}');
							$collection = User::collection();
							$results = $collection->group($keys, $inital, $reduce, $conditions);
						}
				}
			}
		}
		return compact('search', 'results', 'searchType', 'criteria');
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
	public function purchases($eventId = null) {
		if ($eventId) {
			$purchaseHeading = $this->_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->getOrderItems($eventId);
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
										$purchaseOrder[$inc]['Product Name'] = $eventItem['description'];
										$purchaseOrder[$inc]['Product Color'] = $eventItem['color'];
										$purchaseOrder[$inc]['SKU'] = $eventItem['vendor_style'];
										$purchaseOrder[$inc]['Unit'] = $eventItem['sale_whol'];
										if (empty($purchaseOrder[$inc]['Quantity'])) {
											$purchaseOrder[$inc]['Quantity'] = $item['quantity'];
										} else {
											$purchaseOrder[$inc]['Quantity'] += $item['quantity'];
										}
										$purchaseOrder[$inc]['Total'] = $purchaseOrder[$inc]['Quantity'] * $eventItem['sale_whol'];
										$purchaseOrder[$inc]['Size'] = $item['size'];
										$purchaseOrder[$inc] = $this->sortArrayByArray($purchaseOrder[$inc], $purchaseHeading);
									}
								}
							}
							if (!empty($purchaseOrder[$inc]['Total'])) {
								$total['sum'] += $purchaseOrder[$inc]['Total'];
								$total['quantity'] += $purchaseOrder[$inc]['Quantity'];
							}
							++$inc;
						}
					}
				}
			}
		}
		return compact('purchaseOrder', 'event', 'total', 'purchaseHeading');
	}

	public function orders($eventId = null) {
		if ($eventId) {
			$orderHeading = $this->_orderHeading;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->getOrderItems($eventId);
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				$orders = Order::find('all', array(
					'conditions' => array(
						'items.item_id' => (string) $eventItem['_id']
				)));
				if ($orders) {
					$orderData = $orders->data();
					if (!empty($orderData)) {
						foreach ($orderData as $order) {
							$items = $order['items'];
							$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
							$others['Closed'] = 0;
							$others['Open'] = 0;
							foreach ($items as $item) {
								$orderEvent = Event::find('first', array(
									'conditions' => array(
										'_id' => $item['event_id']
								)));
								if (!empty($orderEvent)) {
									$others['Closed'] += ($orderEvent->end_date->sec < time()) ? 1 : 0;
									$others['Open'] += ($orderEvent->end_date->sec > time()) ? 1 : 0;
								}
								if (($item['item_id'] == $eventItem['_id']) && $item['status'] != 'Order Canceled'){
									$orderList[$inc]['Select'] = ($others['Open'] != 0) ? '' : 'Checked';
									$orderList[$inc]['Item'] = $eventItem['_id'];
									$orderList[$inc]['Cart'] = $item['_id'];
									$orderList[$inc]['OrderNum'] = $order['order_id'];
									$orderList[$inc]['id'] = $order['_id'];
									$orderList[$inc]['SKU'] = strtoupper(trim($eventItem['vendor_style'].'-'.$item['size']));
									$orderList[$inc]['Qty'] = $item['quantity'];
									$orderList[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
									$orderList[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
									$orderList[$inc]['Note'] = $others;
									$orderList[$inc] = $this->sortArrayByArray($orderList[$inc], $orderHeading);
								}
								++$inc;
							}
						}
					}
				}
			}
		}
		return compact('orderList', 'event', 'total', 'orderHeading');
	}

	/**
	 * Generates the order file.
	 *
	 * @return array
	 *     orderFile - This is the array of all the orders that are being processed for shipment.
	 *     heading - The column headings for the orderFile.
	 *     event - The event object.
	 */
	public function orderfile() {
		$heading = $this->_fileHeading;
		if ($this->request->data) {
			$eventId = $this->request->data['event_id'];
			unset($this->request->data['event_id']);
			$orders = $this->request->data;
			$inc = 0;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			foreach ($orders as $orderId => $cartId) {
				$conditions = array(
					'_id' => substr($orderId, 0, 24),
					'items._id' => $cartId
				);
				$order = $this->getOrders('first', $conditions);
				$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
				$items = $order['items'];
				foreach ($items as $item) {
					if (($item['_id'] == $cartId)){
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
						$orderFile[$inc]['SKU'] = strtouppper(trim($orderItem->vendor_style.'-'.$item['size']));
						$orderFile[$inc]['Qty'] = $item['quantity'];
						$orderFile[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
						$orderFile[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
						$orderFile[$inc]['Customer PO #'] = '';
						$orderFile[$inc]['Pack Slip Comment'] = '';
						$orderFile[$inc]['Special Packing Instructions'] = '';
						$orderFile[$inc]['Address1'] = $order['shipping']['address'];
						$orderFile[$inc]['Address2'] = $order['shipping']['address_2'];
						$orderFile[$inc]['City'] = $order['shipping']['city'];
						$orderFile[$inc]['StateOrProvince'] = $order['shipping']['state'];
						$orderFile[$inc]['Zip'] = $order['shipping']['zip'];
						$orderFile[$inc]['Ref1'] = $item['item_id'];
						$orderFile[$inc]['Ref2'] = $item['size'];
						$orderFile[$inc]['Ref3'] = $item['color'];
						$orderFile[$inc]['Ref4'] = $item['description'];
						$orderFile[$inc] = array_merge($heading, $orderFile[$inc]);
						$orderFile[$inc] = $this->sortArrayByArray($orderFile[$inc], $heading);
					}
				}
				++$inc;
			}
		}
		return compact('orderFile', 'heading', 'event');
	}

	protected function getOrders($search = 'all', $conditions = array()) {
		$orders = Order::find($search, array(
			'conditions' => $conditions
		));
		return $orders->data();
	}
	
	public function getOrderItems($eventId = null) {
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

	public function productfile($eventId = null) {
		if ($eventId) {
			$productHeading = $this->_productHeading;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = $this->getOrderItems($eventId);
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				foreach ($eventItem['details'] as $key => $value) {
					$conditions = array(
						'items.item_id' => (string) $eventItem['_id'],
						'items.size' => (string) $key,
						'items.status' => array('$ne' => 'Order Canceled')
					);
					$orders = $this->getOrders('all', $conditions);
					if ($orders) {
						foreach ($orders as $order) {
							$items = $order['items'];
							foreach ($items as $item) {
								if (($item['item_id'] == $eventItem['_id']) && ($key == $item['size'])){
									$fields[$inc]['SKU'] = strtoupper(trim($eventItem['vendor_style'].'-'.$item['size']));
									$fields[$inc]['Description'] = strtoupper(substr($eventItem['description'], 0, 40));
									$fields[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
									$fields[$inc]['Description for Customs'] = $eventItem['category'];
									$fields[$inc]['ShipInsValue'] = number_format($eventItem['orig_whol'], 2);
									$fields[$inc]['Ref1'] = $item['item_id'];
									$fields[$inc]['Ref2'] = $item['size'];
									$fields[$inc]['Ref3'] = $item['color'];
									if ((int) $item['product_weight'] > 0) {
										$fields[$inc]['UOM1_Weight'] = number_format($item['product_weight'],2);
									}
									$fields[$inc] = array_merge($productHeading, $fields[$inc]);
									$productFile[$inc] = $this->sortArrayByArray($fields[$inc], $productHeading);
								}
							}
						}
						++$inc;
					}
				}
			}
		}
		return compact('productFile', 'event', 'productHeading');
	}

	public function sales() {
		FlashMessage::clear();
		if ($this->request->data) {
			$dates = $this->request->data;
			if (!empty($dates['min_date']) && !empty($dates['max_date'])) {
				$conditions = array(
					'date_created' => array(
						'$gt' => new MongoDate(strtotime($this->request->data['min_date'])),
						'$lte' => new MongoDate(strtotime($this->request->data['max_date']))
				));
				$orderCollection = Order::collection();
				$orders = $orderCollection->find($conditions);
				$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
				$collection = Report::collection();
				if ($orders) {
					foreach ($orders as $order) {
						$orderSummary = array();
						$items = $order['items'];
						$itemQuantity = 0;
						foreach ($items as $item) {
							$itemQuantity += $item['quantity'];
						}
							$orderSummary['tax'] = $order['tax'];
							$orderSummary['total'] = $order['total'];
							switch($order['shipping']['state']){
								case 'NY':
									$state = 'NY';
									break;
								case 'PA':
									$state = 'PA';
									break;
								default:
									$state = 'Other';
							}
							$orderSummary['state'] = $state;
							$orderSummary['handling'] = $order['handling'];
							$orderSummary['quantity'] = $itemQuantity;
							$orderSummary['date'] = $order['date_created'];
							$orderSummary['report_id'] = $reportId;
						$collection->save($orderSummary);
					}
				}
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.toDateString(),
							'state' : doc.state
						}
					}");
				$inital = array(
					'total' => 0,
					'tax' => 0,
					'handling' => 0,
					'quantity' => 0,
					'count' => 0
				);
				$reduce = new MongoCode('function(doc, prev){
					prev.total += doc.total,
					prev.tax += doc.tax,
					prev.handling += doc.handling,
					prev.quantity += doc.quantity
					prev.count += 1
					}'
				);
				$conditions = array('report_id' => $reportId);
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$details = $results['retval'];
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.toDateString()
						}
					}");
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$summary = $results['retval'];
				$keys = new MongoCode("function(doc){return {}}");
				$total = $collection->group($keys, $inital, $reduce, $conditions);
				$total = $total['retval'][0];
				$collection->remove($conditions);
				if (!empty($summary)) {
					FlashMessage::set('Results Found', array('class' => 'pass'));
				} else {
					FlashMessage::set('No Results Found', array('class' => 'warning'));
				}
			} else {
				FlashMessage::set('Please enter in a valid search date', array('class' => 'warning'));
			}
		}
		return compact('details', 'summary', 'dates', 'total');
	}

	/**
	 * Generates a report of sales grouped by event for a specified date range.
	 *
	 * @return array
	 *    $results
	 *    $dates
	 */
	public function eventSales() {
		FlashMessage::clear();
		if ($this->request->data) {
			$dates = $this->request->data;
			if (!empty($dates['min_date']) && !empty($dates['max_date'])) {
				$conditions = array(
					'date_created' => array(
						'$gt' => new MongoDate(strtotime($this->request->data['min_date'])),
						'$lte' => new MongoDate(strtotime($this->request->data['max_date']))
				));
				$orderCollection = Order::collection();
				$orders = $orderCollection->find($conditions);
				$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
				$collection = Report::collection();
				if ($orders) {
					foreach ($orders as $order) {
						$orderSummary = array();
						$items = $order['items'];
						foreach ($items as $item) {
							$orderItem = array();
							$orderItem['date'] = $order['date_created'];
							$orderItem['quantity'] = $item['quantity'];
							$orderItem['total'] = $item['sale_retail'] * $item['quantity'];
							$orderItem['event_name'] = $item['event_name'];
							$orderItem['report_id'] = $reportId;
							$collection->save($orderItem);
						}
					}
				}
				$keys = new MongoCode("
					function(doc){
						return {
							'event' : doc.event_name
						}
					}");
				$inital = array(
					'total' => 0,
					'quantity' => 0
				);
				$reduce = new MongoCode('function(doc, prev){
					prev.total += doc.total,
					prev.quantity += doc.quantity
					}'
				);
				$conditions = array('report_id' => $reportId);
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$results = $results['retval'];
				$keys = new MongoCode("function(doc){return {}}");
				$total = $collection->group($keys, $inital, $reduce, $conditions);
				$total = $total['retval'][0];
				$collection->remove($conditions);
				if (!empty($results)) {
					FlashMessage::set('Results Found', array('class' => 'pass'));
				} else {
					FlashMessage::set('No Results Found', array('class' => 'warning'));
				}
			} else {
				FlashMessage::set('Please enter in a valid search date', array('class' => 'warning'));
			}
		}
		return compact('results', 'dates', 'total');
	}
}

?>