<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Base;
use MongoCode;
use MongoDate;


class ReportsController extends BaseController {

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

	protected $_dc = 'ALN';

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

	protected $_fileHeading = array(
		'Date',
		'ClientId',
		'DC',
		'ShipMethod',
		'RushOrder (Y/N)',
		'OrderNum',
		'SKU',
		'Qty',
		'CompanyOrName',
		'ContactName',
		'Address1',
		'Address2',
		'City',
		'StateOrProvince',
		'Zip',
		'Country',
		'Email',
		'Tel',
		'Customer PO #',
		'Pack Slip Comment',
		'Special Packing Instructions'
	);


	public function index() {

	}

	public function cart() {
		$this->_render['layout'] = false;
		$y = Cart::count();
		$x = time() * 1000;
		echo "[$x, $y]";
	}

	public function affiliate() {
		$orderTotals = null;
		if ($this->request->data) {
			$affiliate = $this->request->data['affiliate'];
			$min = new MongoDate(strtotime($this->request->data['min_date']));
			$max = new MongoDate(strtotime($this->request->data['max_date']));
			$total = 0;
			$date = array(
				'date_created' => array(
					'$gt' => $min,
					'$lt' => $max)
			);
			switch ($affiliate) {
				case 'trendytogs':
					$conditions = array(
						'trendytogs_signup' => array('$exists' => true),
						"purchase_count" => array('$gte' => 1)
					);
					break;
				default:
					$conditions = array(
						'invited_by' => $affiliate,
						'purchase_count' => array('$gte' => 1)
					);
					break;
			}
			$users = User::find('all', array('conditions' => $conditions));
			$userId = array();
			foreach ($users as $user) {
				$userId[] = (string) $user->_id;
			}
			$keys = new MongoCode('function(doc){
				return {
					Date: doc.date_created.toDateString()
					}
			}');
			$inital = array('total' => 0);
			$reduce = new MongoCode('function(doc, prev){
				prev.total += doc.total
				}'
			);
			$condition = $date + array('user_id' => array('$in' => $userId));

			$collection = Order::collection();
			$retvals = $collection->group($keys, $inital, $reduce, $condition);
			$sum = null;
			$orderTotals = $retvals['retval'];
			if ($orderTotals) {
				foreach ($orderTotals as $totals) {
					$sum += $totals['total'];
				}
			}
		}

		return compact('orderTotals', 'sum');
	}

	public function logistics($event = null) {
		$events = Event::all();
		return compact('events', 'items');
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
									if (($item['item_id'] == $eventItem['_id']) && ($key == $item['size'])){
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
									$orderList[$inc]['OrderNum'] = $order['order_id'];
									$orderList[$inc]['id'] = $order['_id'];
									$orderList[$inc]['SKU'] = $eventItem['vendor_style'];
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
			foreach ($orders as $orderId => $itemId) {
				$conditions = array(
					'_id' => substr($orderId, 0, 24),
					'items.item_id' => $itemId
				);
				$order = $this->getOrders('first', $conditions);
				$orderItem = Item::find('first', array(
					'conditions' => array(
						'_id' => $itemId
				)));
				$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
				$items = $order['items'];
				foreach ($items as $item) {
					if (($item['item_id'] == $itemId)){
						$orderFile[$inc]['ContactName'] = '';
						$orderFile[$inc]['Date'] = date('m/d/Y');
						$orderFile[$inc]['ClientId'] = $this->_clientId;
						$orderFile[$inc]['DC'] = $this->_dc;
						if ($order['shippingMethod'] == 'ups') {
						     $orderFile[$inc]['ShipMethod'] = 'UPSGROUND';
						} else {
						     $orderFile[$inc]['ShipMethod'] = $order['shippingMethod'];
						}
						$orderFile[$inc]['RushOrder (Y/N)'] = '';
						$orderFile[$inc]['Tel'] = $order['shipping']['telephone'];
						$orderFile[$inc]['Country'] = '';
						$orderFile[$inc]['OrderNum'] = $order['order_id'];
						$orderFile[$inc]['SKU'] = $orderItem->vendor_style;
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
									$fields[$inc]['SKU'] = $eventItem['vendor_style'];
									$fields[$inc]['Description'] = strtoupper(substr($eventItem['description'], 0, 40));
									$fields[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
									$fields[$inc]['Description for Customs'] = $eventItem['category'];
									$fields[$inc]['ShipInsValue'] = number_format($eventItem['orig_whol'], 2);
									$fields[$inc]['Ref1'] = $item['item_id'];
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
}


?>